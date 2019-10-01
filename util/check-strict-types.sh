#!/bin/bash

set -e

here="`dirname $0`";
top="$here/..";
err=0;

pushd $top > /dev/null

while read f; do
    if ! grep -q '^\s*declare\s*(\s*strict_types\s*=\s*1\s*)\s*;\s*$' "$f"; then
      echo "sed -e 's|^\s*\(namespace .\+;\)\s*$|declare(strict_types=1);\\n\\n\1|' -i '$f'";
      if (( 0 == $err )); then err=2; fi
    fi
done <<<$( find src/ packages/ app/ -type f -name '*.php' ! -wholename '*/config/*.php' )

popd > /dev/null

exit $err;
