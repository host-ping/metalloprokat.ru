<?php

namespace Brouzie\Components\Indexer\Async;

interface Commands
{
    public const QUEUE_NAME_INDEX = 'brouzie_indexer.index';

    public const QUEUE_NAME_UPDATE = 'brouzie_indexer.update';

    public const REINDEX = 'brouzie_indexer.index.reindex';

    public const REINDEX_IDS = 'brouzie_indexer.index.reindex_ids';

    public const UPDATE = 'brouzie_indexer.update.update';

    public const DELETE = 'brouzie_indexer.index.delete';

    public const CLEAR = 'brouzie_indexer.index.clear';
}
