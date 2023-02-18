#!/bin/sh

rm -rf ./packaging && mkdir ./packaging
mkdir ./packaging/bbpress-allowed-shortcodes
cp -r includes/ packaging/bbpress-allowed-shortcodes/includes/
cp -r views/ packaging/bbpress-allowed-shortcodes/views/
cp bbpress-allowed-shortcodes.php packaging/bbpress-allowed-shortcodes/
cp LICENSE packaging/bbpress-allowed-shortcodes/
cp readme.txt packaging/bbpress-allowed-shortcodes/
cd packaging
zip -r bbpress-allowed-shortcodes.zip .
