<?php

namespace Frc\Satis\Builder;

use Symfony\Component\Finder\Finder;

use Google\Cloud\Storage\StorageClient;
use Aws\S3\S3Client;

class JsonBuilder
{
    protected $name;

    protected $homepage;

    protected $root;

    protected $input;

    protected $folder;

    protected $external;

    protected $output;

    protected $satis = [];

    public function __construct($root)
    {
        $this->root = $root;
    }

    public function from($folder)
    {
        $this->folder = $folder;
        if (preg_match('/gs:\/\//i', $folder) || preg_match('/^S3:\/\//i', $folder)) {
            $this->generate($this->folder);
        } else {
            $this->generate("{$this->root}/{$this->folder}");
        }
        return $this;
    }

    protected function generate($input)
    {
        // For this to work, you need to have google project initiated in this folder
        if (preg_match('/^gs:\/\//i', $input)){
            $storage = new StorageClient();
            $storage->registerStreamWrapper();
        }

        // TODO:AWS Integration has not been tested
        if (preg_match('/^s3:\/\//i', $input)) {
            $s3Client = new S3Client();
            $s3Client->registerStreamWrapper();
        }

        $files = (new Finder)->files()->in($input)->name('*.zip');
        $files = array_keys(iterator_to_array($files));

        $files = array_map(function($path) {
            $basename = basename($path);
            return [
                'url' => $path,
                'version' => $this->parseVersionFromFile($basename),
                'name' => $this->parsePackageNameFromFile($basename),
                'vendor' => basename(dirname($path)),
                'type' => $this->parseTypeFromFile($path),
            ];
        }, $files);

        $files = array_map(function($args) {
            return $this->generatePackageJson($args);
        }, $files);

        $external = $this->getExternalRepos();
        $satis = array_merge($external, $files);

        $this->satis = $this->generateSatisJson($satis);
    }

    public function name($input)
    {
        $this->satis['name'] = $input;
        return $this;
    }

    public function homepage($input)
    {
        $this->satis['homepage'] = $input;
        return $this;
    }

    public function external($input)
    {
        $this->external = $input;
        return $this;
    }

    public function output($input)
    {
        $this->output = $input;
        return $this;
    }

    public function save($name)
    {
        if (preg_match("/^gs:\/\//i", $name) || preg_match("/^S3:\/\//i", $name)) {
            $path = $name;
        } else {
            $path = "{$this->root}/{$name}";
        }
        $file = fopen($path, 'w');
        fwrite(
            $file,
            json_encode($this->satis, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
        fclose($file);
    }

    protected function getExternalRepos()
    {
        if (preg_match("/^gs:\/\//i", $this->external) || preg_match("/^S3:\/\//i", $this->external)) {
            $file = $this->external;
        } else {
            $file = "{$this->root}/{$this->external}";
        }
        $external = file_get_contents($file);
        $repos = json_decode($external, true)['repositories'];

        return $repos;
    }

    protected function parseVersionFromFile($file)
    {
        preg_match('/\d+(\.\d+)+/', $file, $matches);
        return $matches[0];
    }

    protected function parsePackageNameFromFile($file)
    {
        return preg_replace('/[\.\-_][\dv]+(\.\d+)+.[^\.]+$/', '', $file);
    }

    protected function parseTypeFromFile($file)
    {
        preg_match('/packages\/(.[^\/]+)\//', $file, $matches);

        return $matches[1] ?? 'package';
    }

    protected function generatePackageJson($args)
    {
        return [
            "type" => "package",
            "package" => [
                "name" => $args['vendor'] . '/' . $args['name'],
                "version" => $args['version'],
                "type" => $args['type'],
                "dist" => [
                    "url" => $args['url'],
                    "type" => "zip"
                ],
            ],
        ];
    }

    protected function generateSatisJson($pacakges)
    {
        return [
            "name" => $this->name,
            "homepage" => ltrim($this->homepage, '/'),
            "repositories" => $pacakges,
            "archive" => [
                "directory" => 'dist',
                "format" => "tar",
                "skip-dev" => true
            ],
            "require-all" => true,
        ];
    }
}
