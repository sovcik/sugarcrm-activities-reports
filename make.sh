#!/bin/sh

echo "Calculating MD5 hash..."
mfiles=`ls -1 source/*`
for file in $mfiles; do
  md5h=`md5sum $file`
  echo "  > ${file##*/}"
  sed -i -e "/${file##*/}/s|=>.*$|=> \'${md5h:0:32}\',|" source/ActivitiesReports_files.md5 
done

echo "Copying files from source to build folder..."
[-d build] || mkdir build
cp source/* build/

echo "Creating package..."
pkg="SugarCRM-6.x.x-activities-reports.zip"
pushd build
zip -m $pkg *
popd

echo "Package $pkg created."

