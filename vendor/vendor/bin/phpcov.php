#!/usr/bin/env sh
SRC_DIR="`pwd`"
cd "`dirname "$0"`"
cd "../sebastianbergmann/phpcov"
BIN_TARGET="`pwd`/phpcov.php"
cd "$SRC_DIR"
"$BIN_TARGET" "$@"
