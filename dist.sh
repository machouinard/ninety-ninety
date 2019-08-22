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

zip -r ninety-ninety.zip dist
mv ./ninety-ninety.zip dist
