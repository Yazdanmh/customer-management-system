<?php

namespace App\Http\Controllers\backend;
use Illuminate\Support\Facades\{
    DB,
    Auth,
    Storage,
};

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Closure;
use Throwable;

class HelperController extends Controller
{
    public function safeGet(Closure $callback)
    {
        try {
            return $callback();
        } catch (Throwable $e) {
            $this->logActivity(
                'GET Request Error: ' . $e->getMessage(),
                [
                    'user_id' => Auth::id(),
                    'exception' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'url' => request()->fullUrl(),
                    'method' => request()->method(),
                ],
                'System',
                'Safe Get'
            );

            return redirect()->back()->with(['error' => 'Something went wrong:' . $e->getMessage()]);
        }
    }
    public function hasRelatedRecords(string $model, int $id, array $relations): bool
    {
        return collect($relations)->sum(function ($relation) use ($model, $id) {
            return DB::table($relation['table'])->where($relation['column'], $id)->count();
        }) > 0;
    }

    public function logActivity(string $event, array $properties, string $logName, $model = null): void
    {
        $logger = activity()
            ->event($event)
            ->withProperties($properties);

        if ($model instanceof Model) {
            $logger->performedOn($model);
        }

        if (Auth::check()) {
            $logger->causedBy(Auth::user());
        }

        $logger->log($logName);
    }

    public function executeWithTransaction(
        callable $operation,
        string $successLog,
        string $errorLog,
        array $logProperties = [],
        $subject = null
    ) {
        DB::beginTransaction();

        $logName = is_object($subject)
            ? class_basename($subject)
            : (is_string($subject) ? $subject : 'System');

        $logSubject = is_object($subject) ? $subject : null;

        try {
            $result = $operation();
            DB::commit();

            $this->logActivity(
                $successLog,
                $logProperties,
                $logName,
                $logSubject
            );

            return $result;
        } catch (Throwable $e) {
            DB::rollBack();

            $this->logActivity(
                $errorLog . ': ' . $e->getMessage(),
                array_merge($logProperties, [
                    'exception' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]),
                $logName,
                $logSubject
            );

            return request()->expectsJson()
                ? response()->json(['error' => $e->getMessage()], 500)
                : redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function upload(string $dir, $file = null, string $disk = 'public'): string
    {
        if (!$file) {
            return $dir . 'def.png';
        }

        try {
            if (!Storage::disk($disk)->exists($dir)) {
                Storage::disk($disk)->makeDirectory($dir);
            }

            $path = $file->store($dir, $disk);
            return $path;
        } catch (\Exception $e) {
            return $dir . 'def.png';
        }
    }

    public function update(string $dir, $oldFile = null, $newFile = null, string $disk = 'public'): string
    {
        if (!$newFile) {
            return $oldFile; // return existing full path
        }

        try {
            if ($oldFile && Storage::disk($disk)->exists($oldFile)) {
                Storage::disk($disk)->delete($oldFile);
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $this->upload($dir, $newFile, $disk);
    }


    public function delete(string $file = null, array $disks = ['public']): bool
    {
        if (!$file) return false;

        try {
            foreach ($disks as $disk) {
                if (Storage::disk($disk)->exists($file)) {
                    Storage::disk($disk)->delete($file);
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
