#!/usr/bin/env bash

ls -la
ls -la ./..

mkdir -p doc/DHL_ShippingHandover
rst2pdf -b 1 \
        -o doc/DHL_ShippingHandover/DHL_ShippingHandover_Doc.pdf \
        -s build/doc/src/dhl.style build/doc/src/EndUserDoc.rst
cp -r build/doc/DHL_ShippingHandover ./doc

zip -r build/artifact.zip ./* \
    -x composer.json

cp -r ./ ./../new-git

ls -la
ls -la ./..
