"""Global settings to the font processor"""


import logging as _l


# the target folder, where the rult is stored
TARGET_DIR = 'target/'


# base font size
EM_SIZE = 2048


# Pre-rendered PNG images
PNG_WIDTHS = [16, 120]


# we use w=16 icons as source for data URIs and store them in the DB
DB_PNG_WIDTH = 16


# the log level to use
LOG_LEVEL = _l.INFO


# whether the cached entries should be respected
LOAD_CACHE = True


#EOF
