<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class OrthancService
{
    public function client(): PendingRequest
    {
        return Http::withBasicAuth(
            (string) config('orthanc.user'),
            (string) config('orthanc.password')
        )->acceptJson()->timeout(30)->connectTimeout(5);
    }

    public function findStudies(array $query, bool $expand = true): array
    {
        $payload = [
            'Level' => 'Study',
            'Limit' => 1000,
            'Query' => $query,
        ];

        if ($expand) {
            $payload['Expand'] = true;
        }

        $response = $this->client()->post(config('orthanc.url').'/tools/find', $payload);

        if ($response->failed()) {
            throw new \RuntimeException('Orthanc request failed');
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw new \RuntimeException('Invalid Orthanc response');
        }

        return $data;
    }

    public function study(string $id): ?array
    {
        $response = $this->client()->get(config('orthanc.url').'/studies/'.$id);

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json();

        return is_array($data) ? $data : null;
    }

    public function instances(string $studyId): array
    {
        $response = $this->client()->get(config('orthanc.url').'/studies/'.$studyId.'/instances');

        if (! $response->successful()) {
            return [];
        }

        $data = $response->json();

        return is_array($data) ? $data : [];
    }

    public function renderedUrl(string $instanceId): string
    {
        return config('orthanc.url').'/instances/'.$instanceId.'/rendered';
    }

    public function cleanPatientName(?string $rawName): string
    {
        $rawName = $rawName ?: 'غير معروف';

        return preg_replace('/[^a-zA-Z0-9\s]/u', '', $rawName) ?? '';
    }

    public function formatDicomDate(?string $dicomDate): string
    {
        if (! $dicomDate) {
            return '';
        }

        return substr($dicomDate, 0, 4).'/'.substr($dicomDate, 4, 2).'/'.substr($dicomDate, 6, 2);
    }

    public function formatDicomTime(?string $dicomTime): string
    {
        if (! $dicomTime) {
            return '';
        }

        return substr($dicomTime, 0, 2).':'.substr($dicomTime, 2, 2).':'.substr($dicomTime, 4, 2);
    }
}
