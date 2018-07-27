<?php
namespace Capco\AppBundle\GraphQL;

use Capco\AppBundle\Utils\Arr;
use Overblog\GraphQLBundle\Request\Executor;

class ConnectionTraversor
{
    protected $executor;

    public function __construct(Executor $executor)
    {
        $this->executor = $executor;
    }

    public function traverse(
        array &$data,
        string $path,
        callable $callback,
        callable $renewalQuery
    ): void {
        do {
            $connection = Arr::path($data, $path);
            $edges = Arr::path($connection, 'edges');
            $pageInfo = Arr::path($connection, 'pageInfo');
            $endCursor = $pageInfo['endCursor'];
            if (\count($edges) > 0) {
                foreach ($edges as $edge) {
                    $callback($edge);
                    if ($edge['cursor'] === $endCursor) {
                        $data = $this->executor->execute(null, [
                            'query' => $renewalQuery($pageInfo),
                            'variables' => [],
                        ])->toArray();
                    }
                }
            }
        } while (true === $pageInfo['hasNextPage']);
    }
}
