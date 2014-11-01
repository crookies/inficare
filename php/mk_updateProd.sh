#!/bin/sh
#This will update of modified files to the prodution dir for publication
rsync -av . ../build/inficare/production/php

