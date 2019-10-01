#!/bin/bash

set -e

here="`dirname $0`";
top="$here/..";
err=0;

pushd $top > /dev/null

while read f; do
    e=`echo " * @file $f"`;
    l=`head -10 "$f" | awk '/^ \* @file / {print $0}'`;
    if [ "$e" != "$l" ] ; then
      lq=`echo "$l" | sed -e 's#\*#\\\\*#g'`
      eq=`echo "$e" | sed -e 's#\*#\\*#g'`
      echo "sed -e ""'""s#^$lq#$eq#g""'"" -i $f";
      if (( 0 == $err )); then err=2; fi
    fi
done <<<$( find src/ -type f -name '*.php' )

while read f; do
    e=`echo  " * @file $f"`
    e=`echo "$e" | sed -e 's#^ \* @file \.\?packages/[^/]\+/# * @file #'`;
    l=`head -10 "$f" | awk '/^ \* @file / {print $0}'`;
    if [ "$e" != "$l" ] ; then
      lq=`echo "$l" | sed -e 's#\*#\\\\*#g'`
      eq=`echo "$e" | sed -e 's#\*#\\*#g'`
      echo "sed -e ""'""s#^$lq#$eq#g""'"" -i $f";
      if (( 0 == $err )); then err=2; fi
    fi
done <<<$( find packages/ -type f -name '*.php' )

popd > /dev/null

exit $err;
