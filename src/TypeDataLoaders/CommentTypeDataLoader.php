<?php
namespace PoP\Comments\TypeDataLoaders;

use PoP\ComponentModel\TypeDataLoaders\AbstractTypeQueryableDataLoader;

class CommentTypeDataLoader extends AbstractTypeQueryableDataLoader
{
    public function getDataquery()
    {
        return GD_DATAQUERY_COMMENT;
    }

    public function getFilterDataloadingModule(): ?array
    {
        return [\PoP_Comments_Module_Processor_FieldDataloads::class, \PoP_Comments_Module_Processor_FieldDataloads::MODULE_DATALOAD_DATAQUERY_COMMENTLIST_FIELDS];
    }

    public function getObjects(array $ids): array
    {
        $cmscommentsapi = \PoP\Comments\FunctionAPIFactory::getInstance();
        $query = [
            'include' => $ids,
        ];
        return $cmscommentsapi->getComments($query);
    }

    public function getQuery($query_args): array
    {
        $query = parent::getQuery($query_args);

        $query['status'] = POP_COMMENTSTATUS_APPROVED;
        // $query['type'] = 'comment'; // Only comments, no trackbacks or pingbacks
        $query['post-id'] = $query_args[GD_URLPARAM_COMMENTPOSTID];

        return $query;
    }
    public function getDataFromIdsQuery(array $ids): array
    {
        $query = array();
        $query['include'] = $ids;
        return $query;
    }

    public function executeQuery($query, array $options = [])
    {
        $cmscommentsapi = \PoP\Comments\FunctionAPIFactory::getInstance();
        return $cmscommentsapi->getComments($query, $options);
    }

    public function executeQueryIds($query): array
    {
        $options = [
            'return-type' => POP_RETURNTYPE_IDS,
        ];
        return (array)$this->executeQuery($query, $options);
    }
}
