<?php declare(strict_types=1);

namespace Boolfly\Brand\Model\Brand;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Directory\WriteInterface;

class File
{
    /**
     * Path in /pub/media directory
     */
    const ENTITY_MEDIA_PATH = '/catalog/brand';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Filesystem\Driver\File
     */
    private $driverFile;

    /**
     * @var Mime
     */
    private $mime;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var ReadInterface
     */
    private $baseDirectory;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @param Filesystem $filesystem
     * @param Filesystem\Driver\File $driverFile
     * @param DirectoryList $directoryList
     * @param Mime $mime
     */
    public function __construct(
        Filesystem $filesystem,
        Filesystem\Driver\File $driverFile,
        DirectoryList $directoryList,
        Mime $mime
    ) {
        $this->driverFile = $driverFile;
        $this->filesystem = $filesystem;
        $this->directoryList = $directoryList;
        $this->mime = $mime;
    }

    /**
     * Get WriteInterface instance
     *
     * @return WriteInterface
     */
    private function getMediaDirectory()
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }
        return $this->mediaDirectory;
    }

    /**
     * Get Base Directory read instance
     *
     * @return ReadInterface
     */
    private function getBaseDirectory()
    {
        if (!isset($this->baseDirectory)) {
            $this->baseDirectory = $this->filesystem->getDirectoryRead(DirectoryList::ROOT);
        }

        return $this->baseDirectory;
    }

    /**
     * Retrieve MIME type of requested file
     *
     * @param string $fileName
     * @return string
     */
    public function getMimeType($fileName)
    {
        $filePath = $this->getFilePath($fileName);
        $absoluteFilePath = $this->getMediaDirectory()->getAbsolutePath($filePath);

        $result = $this->mime->getMimeType($absoluteFilePath);
        return $result;
    }

    /**
     * Get file statistics data
     *
     * @param string $fileName
     * @return array
     */
    public function getStat($fileName)
    {
        $filePath = $this->getFilePath($fileName);

        $result = $this->getMediaDirectory()->stat($filePath);
        return $result;
    }

    /**
     * Check if the file exists
     *
     * @param string $fileName
     * @return bool
     */
    public function isExist($fileName)
    {
        $filePath = $this->getFilePath($fileName);

        $result = $this->getMediaDirectory()->isExist($filePath);
        return $result;
    }

    /**
     * Construct and return file subpath based on filename relative to media directory
     *
     * @param string $fileName
     * @return string
     */
    private function getFilePath($fileName)
    {
        $filePath = ltrim($fileName, '/');

        $mediaDirectoryRelativeSubpath = $this->getMediaDirectoryPathRelativeToBaseDirectoryPath();
        $isFileNameBeginsWithMediaDirectoryPath = $this->isBeginsWithMediaDirectoryPath($fileName);

        // if the file is not using a relative path, it resides in the catalog/category media directory
        $fileIsInCategoryMediaDir = !$isFileNameBeginsWithMediaDirectoryPath;

        if ($fileIsInCategoryMediaDir) {
            $filePath = self::ENTITY_MEDIA_PATH . '/' . $filePath;
        } else {
            $filePath = substr($filePath, strlen($mediaDirectoryRelativeSubpath));
        }

        return $filePath;
    }

    /**
     * Checks for whether $fileName string begins with media directory path
     *
     * @param string $fileName
     * @return bool
     */
    public function isBeginsWithMediaDirectoryPath($fileName)
    {
        $filePath = ltrim($fileName, '/');

        $mediaDirectoryRelativeSubpath = $this->getMediaDirectoryPathRelativeToBaseDirectoryPath();
        $isFileNameBeginsWithMediaDirectoryPath = strpos($filePath, $mediaDirectoryRelativeSubpath) === 0;

        return $isFileNameBeginsWithMediaDirectoryPath;
    }

    /**
     * Get media directory subpath relative to base directory path
     *
     * @return string
     */
    private function getMediaDirectoryPathRelativeToBaseDirectoryPath()
    {
        $baseDirectoryPath = $this->getBaseDirectory()->getAbsolutePath();
        $mediaDirectoryPath = $this->getMediaDirectory()->getAbsolutePath();

        $mediaDirectoryRelativeSubpath = substr($mediaDirectoryPath, strlen($baseDirectoryPath));

        return $mediaDirectoryRelativeSubpath;
    }

    /**
     * @param $path
     * @return string
     */
    public function getMediaAbsolutePath($path)
    {
        return $this->getMediaDirectory()->getAbsolutePath($path);
    }

    /**
     * @param $fileName
     * @return bool
     */
    public function isFile($fileName)
    {
        $filePath = $this->getFilePath($fileName);
        $result = $this->getMediaDirectory()->isFile($filePath);
        return $result;
    }

    /**
     * @param $fileName
     * @throws FileSystemException
     */
    public function delete($fileName)
    {
        $filePath = $this->getFilePath($fileName);
        $this->getMediaDirectory()->delete($filePath);
    }

    /**
     * @param $image
     * @throws FileSystemException
     */
    public function cleanupCacheImages($image)
    {
        $directoryPath = $this->directoryList->getPath(DirectoryList::MEDIA);
        $directoryPath = $directoryPath . '/' . self::ENTITY_MEDIA_PATH . '/cache';
        if ($this->getMediaDirectory()->isExist($directoryPath)) {
            $files = $this->driverFile->readDirectory($directoryPath);
            foreach ($files as $file) {
                $file = $file . '/' . $image;
                if ($this->getMediaDirectory()->isFile($file)) {
                    $this->driverFile->deleteFile($file);
                }
            }
        }
    }
}
