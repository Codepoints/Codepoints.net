#!/bin/bash

OCP=$1
TARGET_DIR=$2
WIDTH=$3

DB=0
if [[ $4 == "--db" ]]; then
  DB=1
fi

TARGET=$(printf '%simages/%02X/%04X' \
  "$TARGET_DIR" $(( $OCP / 0x1000)) $OCP )

inkscape --without-gui --export-background-opacity=0 \
  --export-png="$TARGET.$WIDTH.png" --export-width=$WIDTH \
  "$TARGET.svgz" 1>&2

optipng -quiet -o7 "$TARGET.$WIDTH.png"

if [[ $DB == 1 ]]; then
  SQL=$(printf '%ssql/%02X_img.sql' "$TARGET_DIR" $(( $OCP / 0x1000 )) )
  touch $SQL
  cat \
    <(echo -n "INSERT OR REPLACE INTO codepoint_image (cp, image) VALUES ($OCP, '") \
    <(base64 -w0 "$TARGET.$WIDTH.png") \
    <(echo "');") \
    >> $SQL
fi
