#!/bin/bash

# base64 of an empty Symbola image
EMPTY=iVBORw0KGgoAAAANSUhEUgAAABAAAAAQEAQAAADlauupAAAACXZwQWcAAAAQAAAAEABcxq3DAAAAjElEQVQ4y92SMQ5AQBBFX1iidAXRUGhotK5AtYmLKBxhOyqd1qmcQqFRKLTMRiRe94s3ycx8eJcggDwHpQRy20JRQJpC38M0PZDjGJYFPO/MjgPbBlV1c4DrgtZXThLYd4gi4S2GAYwRymUJ83yuIcIY8H2LV3adZReahg/JMhhHYRMB6hrWFcKQ/3IA1skWQYhPvHUAAAAASUVORK5CYII=

# create a 16x16 px image with one single character U+$2 in
# font $1
convert -background none -gravity center -size 16x16 -fill black \
    -font "$1" -pointsize 16 \
    label:"$(printf '\U'"$2")" \
    "smp/U+$2.tmp.png" && \
pngcrush -q -rem alla "smp/U+$2.tmp.png" "smp/U+$2.png" && \
/bin/rm "smp/U+$2.tmp.png"

if [ $(base64 -w0 "smp/U+$2.png") = $EMPTY ]; then
  echo "smp/U+$2.png is empty" >&2
  /bin/rm "smp/U+$2.png"
fi

# if we need to reposition the glyph:
#    -gravity SouthWest -splice 1x0 \
#    -gravity NorthEast -chop 1x0 \
#    +repage \
