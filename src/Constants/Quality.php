<?php

namespace Guillaumetissier\ImageResizer\Constants;

enum Quality: int
{
    case MIN = 0;
    case THUMBNAILS = 60;
    case WEB = 80;
    case PRINT = 90;
    case MAX = 100;
}
