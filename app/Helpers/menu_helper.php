<?php

function active_menu($segment)
{
    return service('uri')->getSegment(1) === $segment ? 'active' : '';
}
