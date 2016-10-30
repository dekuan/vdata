#!/usr/bin/env bash

java -jar "tools/yuicompressor-2.4.8.jar" "dist/js/angular/ng-vdata.js" -o "dist/js/angular/ng-vdata.min.js" --charset utf-8
java -jar "tools/yuicompressor-2.4.8.jar" "dist/js/jquery/vdata.js" -o "dist/js/jquery/vdata.min.js" --charset utf-8
