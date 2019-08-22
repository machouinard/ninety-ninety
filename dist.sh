#!/bin/bash

rm -rf ./dist
mkdir ./dist
cp -R ./assets dist
cp -R ./inc dist
cp -R ./lang dist
cp -R ./templates dist
cp ./index.php dist
cp ./ninety-ninety.php dist
cp ./Readme.md dist

mkdir ninety-ninety
cp -R dist/* ninety-ninety

zip -r ninety-ninety.zip ninety-ninety
mv ./ninety-ninety.zip dist

rm -rf ./ninety-ninety
