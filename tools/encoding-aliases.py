#!/usr/bin/python


import re
import sys


def format_bytes(n):
    """"""
    return reduce(lambda x, y: x if not x and y == '00' else y if not x else x+' '+y,
        re.findall('..?', '{0:0100X}'.format(n)), '') or '00'

def encode(encoding, pointer, codepoint):
    """"""
    if encoding in ('ibm866', 'iso-8859-2', 'iso-8859-3', 'iso-8859-4',
            'iso-8859-5', 'iso-8859-6', 'iso-8859-7', 'iso-8859-8',
            'iso-8859-8-i', 'iso-8859-10', 'iso-8859-13', 'iso-8859-14',
            'iso-8859-15', 'iso-8859-16', 'koi8-r', 'koi8-u', 'macintosh',
            'windows-874', 'windows-1250', 'windows-1251', 'windows-1252',
            'windows-1253', 'windows-1254', 'windows-1255', 'windows-1256',
            'windows-1257', 'windows-1258', 'x-mac-cyrillic',):
        # single-byte encoding
        return '{:02X}'.format(pointer + 0x80)
    elif encoding == 'big5':
        lead = pointer / 157 + 0x81
        if lead < 0xA1:
            return False
        trail = pointer % 157
        offset = 0x62
        if trail < 0x3F:
            offset = 0x40
        return '{:02X}{:02X}'.format(lead, trail + offset)
    elif encoding == 'jis0208': # alias euc-jp
        if codepoint == 0xA5:
            return '5C'
        elif codepoint == 0x203E:
            return '7E'
        elif 0xFF61 <= codepoint <= 0xFF9F:
            return '8E {:02X}'.format(codepoint - 0xFF61 + 0xA1)
        else:
            lead = pointer / 94 + 0xA1
            trail = pointer % 94 + 0xA1
            return '{:02X}{:02X}'.format(lead, trail)
    elif encoding == 'euc-kr':
        lead = pointer / 190 + 0x81
        trail = pointer % 190 + 0x41
        return '{:02X}{:02X}'.format(lead, trail)
    return False


filename = sys.argv[1]
encoding = re.sub(r'^encoding/index-(.+)\.txt', r'\1', filename)

with open(filename) as file_:
    for line in file_:
        match = re.match(r'^ *([0-9]+)\t0x([0-9A-F]{1,6}).*$', line)
        if match:
            encoded_value = encode(encoding, int(match.group(1)), int(match.group(2), 16))
            if encoded_value:
                print ('INSERT INTO codepoint_alias (cp, alias, "type") '
                        'VALUES ({}, \'{}\', \'enc:{}\');').format(
                        int(match.group(2), 16),
                        encoded_value,
                        encoding
                    )
