<?php

declare(strict_types=1);
try {
    $a = (zlib_decode(file_get_contents('C:\Users\lenovo\Downloads\save.jkr')));
    $a = str_replace(
        '["iconpack_sigil"]=0.0871611238094',
        '["iconpack_sigil"]=0.8871611238094',
        $a,
    );
    file_put_contents('C:\Users\lenovo\Downloads\save.jkr', zlib_encode($a,  ZLIB_ENCODING_GZIP));
    echo zlib_decode(file_get_contents('C:\Users\lenovo\Downloads\save.jkr'));
} catch (\Throwable $e) {
    print_r($e->getMessage());
}
?>