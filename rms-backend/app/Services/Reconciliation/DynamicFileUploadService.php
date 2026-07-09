<?php

namespace App\Services\Reconciliation;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Services\Audit\AuditLogService;

class DynamicFileUploadService
{
    public function __construct(
        private FilenameParserService $filenameParser,
        private SourceResolverService $sourceResolver,
        private StorageManagerService $storageManager,
        private SourceFileRegistrationService $registrationService,
        private AuditLogService $auditLog
    ){}

    public function upload(UploadedFile $file): array
    {
        return DB::transaction(function () use ($file) {
            $parsed = $this->filenameParser->parse(
                $file->getClientOriginalName()
            );

            $source = $this->sourceResolver->resolve(
                $parsed['source_name']
            );

            $stored = $this->storageManager->store(
                $file,
                $source,
                $parsed
            );

            $sourceFile = $this->registrationService->register(
                $source,
                $parsed,
                $stored
            );

            $sourceFile->update([
                'status' => 'RECEIVED',
                'file_status' => 'AVAILABLE',
                'processing_status' => 'NOT_STARTED',
                'reconciliation_status' => 'NOT_USED',

                'valid_records' => 0,
                'invalid_records' => 0,
                'duplicate_records' => 0,

                'uploaded_by' => auth()->id(),
                'uploaded_ip' => request()->ip(),

                'is_locked' => false,
            ]);

            $sourceFile->refresh();

            $this->auditLog->log(
                'UPLOAD',
                'FILE_UPLOAD',
                'Uploaded ' . $sourceFile->file_name .
                ' (' . $source->source_name . ')'
            );

            return [
                'status' => 'success',
                'message' => 'File uploaded and registered successfully.',
                'data' => [
                    'source_file_id' => $sourceFile->id,
                    'source_id' => $source->id,
                    'source_name' => $source->source_name,
                    'source_type' => $source->source_type,
                    'file_name' => $sourceFile->file_name,
                    'file_type' => $sourceFile->file_type,
                    'business_date' => optional($sourceFile->business_date)->format('Y-m-d'),
                    'business_month' => $sourceFile->business_month,
                    'file_size' => $sourceFile->file_size,
                    'checksum' => $sourceFile->checksum,

                    'status' => $sourceFile->status,
                    'file_status' => $sourceFile->file_status,
                    'processing_status' => $sourceFile->processing_status,
                    'reconciliation_status' => $sourceFile->reconciliation_status,
                ],
            ];
        });
    }
}