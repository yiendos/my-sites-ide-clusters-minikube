<?php

namespace Yiendos\MySitesIde\Clusters\Minikube\Traits;

trait InteractsWithMinikube
{
    /**
     * The my-sites-ide project root - the CLI exports IDE_ROOT for plugins
     *
     * @return string
     */
    protected function ideRoot(): string
    {
        return getenv('IDE_ROOT') ?: getcwd();
    }

    /**
     * A site's deployment file, e.g. Repos/<site>/_build/deployment/local.yaml -
     * written by deploy:k8s-bootstrap
     *
     * @param string $site
     * @param string $file
     * @return string
     */
    protected function deploymentFile(string $site, string $file): string
    {
        return $this->ideRoot() . "/Repos/{$site}/_build/deployment/{$file}";
    }

    /**
     * A helm chart shipped with this plugin, e.g. services/storage
     *
     * @param string $chart
     * @return string
     */
    protected function chartPath(string $chart): string
    {
        return dirname(__DIR__, 2) . "/services/{$chart}";
    }
}
