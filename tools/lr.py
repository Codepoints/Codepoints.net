#!/usr/bin/python


import fontforge as ff
import os
import subprocess
import sys


font = 'fonts/Unidings.ttf'
folder = 'block_images2'
selection = ff.open(font).selection.all()
for c in range(0xF200, 0xF380) + range(0xF400, 0xF580):
    if selection[c]:
        fn = "%s/%s.png" % (folder, c)
        tfn = "%s/%s.tmp.png" % (folder, c)
        subprocess.call([
                "convert", "-background", "none", "-gravity", "center",
                "-size", "128x128", "-fill", "black", "-font", font,
                "-pointsize", "128", "label:"+unichr(c).encode("UTF-8"),
                tfn])
        subprocess.call(["pngcrush", "-q", "-rem", "alla", tfn, fn])
        subprocess.call(["rm", tfn])
    else:
        print '%s: n' % c


