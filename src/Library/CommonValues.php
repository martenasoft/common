<?php

namespace MartenaSoft\Common\Library;

interface CommonValues
{
    public const SITE_PAGINATION_LIMIT = 10;
    public const ADMIN_PAGINATION_LIMIT = 10;
    public const ACTIVE_URL_PARAM_NAME = 'act_';
    public const SEARCH_URL_PARAM_NAME = 'w';

    public const FLASH_SUCCESS_TYPE = 'success';
    public const FLASH_ERROR_TYPE = 'error';
    public const FLASH_ERROR_SYSTEM_MESSAGE = 'System error. Read logs';
    public const FLUSH_SUCCESS_DELETE_MESSAGE = 'Item deleted success';
    public const ERROR_FORM_SAVE_LOGGER_MESSAGE = 'form save error';
    public const ERROR_DELETE_SAVE_LOGGER_MESSAGE = 'form delete error';
    public const ERROR_ENTITY_RECORD_NOT_FOUND = 'record not found';
    public const DEFAULT_DATA_NAME = 'default';

}
