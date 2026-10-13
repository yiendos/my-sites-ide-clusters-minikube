# my-sites-ide-clusters-minikube

A [my-sites-ide](https://github.com/yiendos/my-sites-ide) plugin for [Minikube](https://minikube.sigs.k8s.io/), a single-node Kubernetes cluster on your own machine. It's set up to mimic a DigitalOcean cluster, so you can try deployments locally before you push to production.

The plugin *provides* the cluster. Building images and pushing a site with helm is a deploy plugin's job, e.g. `onsidelive/deploy`. Each works without the other, and they work together when both are installed.

Minikube runs on your host, not in the my-sites-ide compose stack, so this plugin has no containers of its own.

## Install

In your git-ignored `composer.local.json`:

```json
"require": {
    "yiendos/my-sites-ide-clusters-minikube": "@dev"
}
```

then `composer update` and `php my-sites-ide ide:plugin-env` to see its settings.

You need some virtualisation (Docker, QEMU, Hyperkit, VirtualBox...), plus `minikube`, `kubectl` and `helm` on your host. `clusters:minikube-system-requirements` checks them for you.

## Getting started

1. Check your system, and optionally start straight away:

    `php my-sites-ide clusters:minikube-system-requirements`

2. Or start and configure the cluster for a site yourself:

    `php my-sites-ide clusters:minikube-start-configure <site>`

    This starts minikube and creates the persistent volume folders from the site's `create-storage.sh`. It installs a storage class that mimics DigitalOcean's `do-block-storage`, and installs cert-manager.

3. Build your site's images, load them into the cluster, and deploy. With [yiendos/my-sites-ide-deploy-k8s](https://github.com/yiendos/my-sites-ide-deploy-k8s) installed, `deploy:k8s-build-push <site> local` does all three.

The site needs `Repos/<site>/_build/deployment/create-storage.sh` and `local.yaml`, which `deploy:k8s-bootstrap <site>` writes (it runs automatically for new sites when the deploy plugin is installed).

Supporting services (databases, Redis, mail) aren't deployed to the cluster. Sites connect to the ones my-sites-ide already runs, through `host.minikube.internal`. That mirrors production, where they'd be managed services.

## Commands

| Command | What it does |
|---|---|
| `clusters:minikube-system-requirements` | Check for virtualisation, minikube, kubectl and helm (adds the jetstack helm repo if missing) |
| `clusters:minikube-start-configure <site>` | One line: `up`, `storage`, `cert-manager`, `next-steps` |
| `clusters:minikube-up <site>` | Start minikube and create the site's persistent volume folders |
| `clusters:minikube-storage <site>` | Install the mock DigitalOcean storage class |
| `clusters:minikube-cert-manager` | Install cert-manager from the jetstack helm repo |
| `clusters:minikube-load-images <site> [image...]` | Load images into minikube - the ones given (`deploy:k8s-build-push` passes exactly what it built), else the site's `fpm-server`, `web-server`, `cron` and `cli` `-latest` images from `MINIKUBE_REGISTRY` |
| `clusters:minikube-port-forward <site>` | Serve the site on http://localhost:8090 |
| `clusters:minikube-dashboard` | Open the Kubernetes dashboard |
| `clusters:minikube-next-steps <site>` | What to run once the cluster is up |
| `clusters:minikube-destroy` | Delete every minikube cluster |

Every command prints the minikube, kubectl or helm command it runs, so you learn them as you go. The dashboard does the same, showing the `kubectl` command behind each action.

## Settings

Defaults live in this plugin's `.env`; override any of them in the root `.env`.

| Variable | Default | |
|---|---|---|
| `MINIKUBE_REGISTRY` | none | only for `clusters:minikube-load-images` without image references - loads `<registry>/<site>:<image>-latest`, e.g. `registry.digitalocean.com/<your-registry>` |
| `MINIKUBE_ADDONS` | `dashboard,metrics-server,traefik` | addons `clusters:minikube-up` enables |
| `MINIKUBE_CONTAINER_RUNTIME` | `containerd` | |
| `MINIKUBE_CERT_MANAGER_VERSION` | `v1.15.2` | jetstack cert-manager chart version |
| `MINIKUBE_PORT_FORWARD` | `8090` | localhost port for `clusters:minikube-port-forward` |

## Upgrading from `onsidelive/deploy`'s `minikube:*` commands

These commands used to ship inside `onsidelive/deploy` as `minikube:<action>`. They're now `clusters:minikube-<action>`, e.g. `minikube:up` → `clusters:minikube-up`.
