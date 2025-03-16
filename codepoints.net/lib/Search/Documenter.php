<?php

namespace Codepoints\Search;

use Analog\Analog;
use Codepoints\Database;
use Codepoints\Unicode\Codepoint;
use Codepoints\Unicode\CodepointInfo\Aliases;
use Codepoints\Unicode\CodepointInfo\CLDR;
use Codepoints\Unicode\CodepointInfo\Confusables;
use Codepoints\Unicode\CodepointInfo\Extra;
use Codepoints\Unicode\CodepointInfo\Properties;
use Codepoints\Unicode\CodepointInfo\Representation;
use Codepoints\Unicode\CodepointInfo\Wikipedia;
use Codepoints\Unicode\PropertyInfo;


/**
 * create the search document to feed into the fulltext index
 *
 * The search index is a simple table that contains codepoints and their
 * search terms: name, abstract, ... We create it from the other information
 * stored in the ucd.sqlite database. Some, like abstract and kDefinition,
 * get split, stopwords removed and inserted in pieces.
 *
 * The terms are weighted. That means, each term has an associated number
 * representing its importance for the codepoint. Names get the highest weight,
 * words in the Wikipedia abstract the lowest. This ensures, that the search
 * for "ox" finds U+1F402 OX before any other codepoint that happens to
 * relate to oxen.
 */
class Documenter {

    /**
     * @param Array{db: Database, lang: string, info: PropertyInfo} $env
     */
    public function __construct(private Array $env) {
        /**
         * for the search index we hard-code the language to English.
         * TODO check if we can internationalize search somehow
         */
        $env['lang'] = 'en';
        new Aliases($env);
        new CLDR($env);
        new Confusables($env);
        new Extra($env);
        new Properties($env);
        new Representation($env);
        new Wikipedia($env);
    }

    /**
     * @return string
     */
    public function create(Codepoint $cp) : string {
        $repr = $cp->representation;

        $props = [];
        foreach ($cp->properties as $key => $value) {
            $is_bool = in_array($key, $this->env['info']->booleans);
            if (! $is_bool || $value) {
                /* note the property itself as present, e.g. "Emoji", unless
                 * it is a falsy boolean value */
                $props[] = $key;
            }
            if ($key === 'unikemet') {
                foreach ($value as $subkey => $subvalue) {
                    if ($subkey === 'kEH_Desc') {
                        /* add the hieroglyph plain-text description
                         * without the property prefix, but note that we do
                         * have the property set. */
                        $props[] = "prop_{$subkey}";
                        $props[] = $subvalue;
                    } else {
                        $props[] = "prop_{$subkey}_{$subvalue}";
                    }
                }
            } elseif (is_array($value)) {
                foreach ($value as $subvalue) {
                    $props[] = "prop_{$key}_{$subvalue}";
                }
            } else {
                $props[] = "prop_{$key}_{$value}";
            }
        }
        $props =join(' ', $props);

        $chr = $cp->chr();
        $name = $cp->name;
        $name_no_dash = str_replace('-', '', (string)$cp->name);
        $na1 = $cp->properties['na1'] ?? '';
        $kDefinition = $cp->properties['kDefinition'] ?? '';
        $hex = sprintf('%X', $cp->id);
        $zhex = sprintf('%04X', $cp->id);
        $aliases = join(' ', array_map(fn(Array $item) => $item['type'].': '.$item['alias'], $cp->aliases));
        $wikipedia = trim((string)preg_replace('/\s+/', ' ', strip_tags(str_replace('>', '> ', $cp->wikipedia['abstract'] ?? ''))));
        if ($wikipedia) {
            $wikipedia .= ' Wikipedia';
        }
        $tags = join(' ', $cp->cldr['tags']);
        $sc = ($cp->properties['sc']??'') . ' ' . join(' ', $cp->properties['scx']??[]).
              'sc_' .($cp->properties['sc']??'') . ' sc_' . join(' sc_', $cp->properties['scx']??[]);
        $dm = $cp->properties['dt'] === 'none'? [] : (is_array($cp->properties['dm'] ?? null)? $cp->properties['dm'] : [$cp->properties['dm']]);
        $decomp = join(' ', array_map(function(Codepoint $item) : string { return $item->chr(); }, $dm));
        $confusables = $cp->confusables? '1' : '0';
        try {
            $block = $cp->block;
        } catch(\Exception $e) {
            $block = 'noblock';
        }

        /* this document reads very cryptic, but it is somewhat
         * straight-forward. Take each information and repeat it based on its
         * importance. The name, for example, appears 8 times, the Wikipedia
         * abstract only once. */
        return <<<EOD
$chr $chr $chr $chr $chr $chr $chr $chr
na_$name $name $name $name $name $name $name $name
$name_no_dash
na1_$na1 $na1 $na1 $na1 $na1 $na1
kDefinition_$kDefinition $kDefinition $kDefinition $kDefinition
$cp $cp $cp
{$repr('UTF-8')} {$repr('UTF-16')} {$repr('JSON')} {$repr('Ruby')}
int_{$cp->id} {$cp->id}
$zhex hex_$hex hex_$zhex 0x$hex 0x$zhex
$props $props $props
$aliases $aliases
{$cp->extra}
$wikipedia
{$cp->cldr['tts']} {$cp->cldr['tts']} {$cp->cldr['tts']}
$tags $tags
{$block} blk_{$block}
$sc
$decomp
confusables_$confusables
EOD;
    }

    /**
     * update the search index for the next 1000 codepoints
     *
     * Outdated entries are recognized by search_index.version being unequal
     * to the SOFTWARE_VERSION constant.
     */
    public function buildNext() : bool {
        $cp_list = $this->env['db']->getAll('
            SELECT c.cp AS cp, c.name AS name, c.gc AS gc
            FROM codepoints c
            LEFT JOIN search_index s
                USING (cp)
            WHERE s.version IS NULL OR s.version != ?
            LIMIT 1000', SOFTWARE_VERSION);
        $insert = $this->env['db']->prepare('
            INSERT INTO search_index (cp, text, version)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE
                text = VALUE(text),
                version = VALUE(version)'
            );
        /** @psalm-suppress RiskyTruthyFalsyComparison */
        if (! $insert || ! $cp_list) {
            return false;
        }
        foreach ($cp_list as $cp) {
            $doc = $this->create(Codepoint::getCached($cp, $this->env['db']));
            $insert->execute([$cp['cp'], $doc, SOFTWARE_VERSION]);
            Analog::log(sprintf('update search doc for U+%04X', $cp['cp']));
        }
        return true;
    }

}
