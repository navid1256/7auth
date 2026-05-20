<?php

function assets(string $path): string
{
    return site_url('assets/' . $path);
}
function site_url(string $path): string
{
    return BASE_URL . $path;
}
