<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 09/02/2018
 * Time: 20:51
 */

namespace Tangram\Framework;


use Tangram\Package\Link;
use Tangram\Package\PackageInterface;
use Tangram\Repository\RepositoryInterface;

class Framework implements PackageInterface {

    private $type;
    private $targetDir;
    private $installationSource;
    private $sourceType;
    private $sourceUrl = 'https://github.com/nxlib/tangram-framework.git';
    private $sourceReference = 'master';
    private $sourceMirrors;
    private $distType;
    private $distUrl;
    private $distReference;
    private $distSha1Checksum;
    private $distMirrors;
    private $version;
    private $prettyVersion = 'non-normalized';
    private $releaseDate;
    private $extra = array();
    private $binaries = array();
    private $dev;
    private $stability;
    private $notificationUrl;

    public function __construct() {
        $this->releaseDate = new \DateTime();
    }

    /**
     * Returns the package's name without version info, thus not a unique identifier
     *
     * @return string package name
     */
    public function getName() {
        // TODO: Implement getName() method.
    }

    /**
     * Returns the package's pretty (i.e. with proper case) name
     *
     * @return string package name
     */
    public function getPrettyName() {
        // TODO: Implement getPrettyName() method.
    }

    /**
     * Returns a set of names that could refer to this package
     *
     * No version or release type information should be included in any of the
     * names. Provided or replaced package names need to be returned as well.
     *
     * @return array An array of strings referring to this package
     */
    public function getNames() {
        // TODO: Implement getNames() method.
    }

    /**
     * Allows the solver to set an id for this package to refer to it.
     *
     * @param int $id
     */
    public function setId($id) {
        // TODO: Implement setId() method.
    }

    /**
     * Retrieves the package's id set through setId
     *
     * @return int The previously set package id
     */
    public function getId() {
        // TODO: Implement getId() method.
    }

    /**
     * Returns whether the package is a development virtual package or a concrete one
     *
     * @return bool
     */
    public function isDev() {
        // TODO: Implement isDev() method.
    }

    /**
     * Returns the package type, e.g. library
     *
     * @return string The package type
     */
    public function getType() {
        // TODO: Implement getType() method.
    }

    /**
     * Returns the package targetDir property
     *
     * @return string The package targetDir
     */
    public function getTargetDir() {
        // TODO: Implement getTargetDir() method.
    }

    /**
     * Returns the package extra data
     *
     * @return array The package extra data
     */
    public function getExtra() {
        // TODO: Implement getExtra() method.
    }

    /**
     * Sets source from which this package was installed (source/dist).
     *
     * @param string $type source/dist
     */
    public function setInstallationSource($type) {
        // TODO: Implement setInstallationSource() method.
    }

    /**
     * Returns source from which this package was installed (source/dist).
     *
     * @return string source/dist
     */
    public function getInstallationSource() {
        // TODO: Implement getInstallationSource() method.
    }

    /**
     * Returns the repository type of this package, e.g. git, svn
     *
     * @return string The repository type
     */
    public function getSourceType() {
        // TODO: Implement getSourceType() method.
    }

    /**
     * Returns the repository url of this package, e.g. git://github.com/naderman/composer.git
     *
     * @return string The repository url
     */
    public function getSourceUrl() {
        return $this->sourceUrl;
    }

    /**
     * Returns the repository urls of this package including mirrors, e.g. git://github.com/naderman/composer.git
     *
     * @return array
     */
    public function getSourceUrls() {
        // TODO: Implement getSourceUrls() method.
    }

    /**
     * Returns the repository reference of this package, e.g. master, 1.0.0 or a commit hash for git
     *
     * @return string The repository reference
     */
    public function getSourceReference() {
        return $this->sourceReference;
    }

    /**
     * Returns the source mirrors of this package
     *
     * @return array|null
     */
    public function getSourceMirrors() {
        // TODO: Implement getSourceMirrors() method.
    }

    /**
     * Returns the type of the distribution archive of this version, e.g. zip, tarball
     *
     * @return string The repository type
     */
    public function getDistType() {
        // TODO: Implement getDistType() method.
    }

    /**
     * Returns the url of the distribution archive of this version
     *
     * @return string
     */
    public function getDistUrl() {
        // TODO: Implement getDistUrl() method.
    }

    /**
     * Returns the urls of the distribution archive of this version, including mirrors
     *
     * @return array
     */
    public function getDistUrls() {
        // TODO: Implement getDistUrls() method.
    }

    /**
     * Returns the reference of the distribution archive of this version, e.g. master, 1.0.0 or a commit hash for git
     *
     * @return string
     */
    public function getDistReference() {
        // TODO: Implement getDistReference() method.
    }

    /**
     * Returns the sha1 checksum for the distribution archive of this version
     *
     * @return string
     */
    public function getDistSha1Checksum() {
        // TODO: Implement getDistSha1Checksum() method.
    }

    /**
     * Returns the dist mirrors of this package
     *
     * @return array|null
     */
    public function getDistMirrors() {
        // TODO: Implement getDistMirrors() method.
    }

    /**
     * Returns the version of this package
     *
     * @return string version
     */
    public function getVersion() {
        // TODO: Implement getVersion() method.
    }

    /**
     * Returns the pretty (i.e. non-normalized) version string of this package
     *
     * @return string version
     */
    public function getPrettyVersion() {
        return $this->prettyVersion;
    }

    /**
     * Returns the pretty version string plus a git or hg commit hash of this package
     *
     * @see getPrettyVersion
     *
     * @param  bool $truncate If the source reference is a sha1 hash, truncate it
     *
     * @return string version
     */
    public function getFullPrettyVersion($truncate = TRUE) {
        // TODO: Implement getFullPrettyVersion() method.
    }

    /**
     * Returns the release date of the package
     *
     * @return \DateTime
     */
    public function getReleaseDate() {
        return $this->releaseDate;
    }

    /**
     * Returns the stability of this package: one of (dev, alpha, beta, RC, stable)
     *
     * @return string
     */
    public function getStability() {
        // TODO: Implement getStability() method.
    }

    /**
     * Returns a set of links to packages which need to be installed before
     * this package can be installed
     *
     * @return Link[] An array of package links defining required packages
     */
    public function getRequires() {
        // TODO: Implement getRequires() method.
    }

    /**
     * Returns a set of links to packages which must not be installed at the
     * same time as this package
     *
     * @return Link[] An array of package links defining conflicting packages
     */
    public function getConflicts() {
        // TODO: Implement getConflicts() method.
    }

    /**
     * Returns a set of links to virtual packages that are provided through
     * this package
     *
     * @return Link[] An array of package links defining provided packages
     */
    public function getProvides() {
        // TODO: Implement getProvides() method.
    }

    /**
     * Returns a set of links to packages which can alternatively be
     * satisfied by installing this package
     *
     * @return Link[] An array of package links defining replaced packages
     */
    public function getReplaces() {
        // TODO: Implement getReplaces() method.
    }

    /**
     * Returns a set of links to packages which are required to develop
     * this package. These are installed if in dev mode.
     *
     * @return Link[] An array of package links defining packages required for development
     */
    public function getDevRequires() {
        // TODO: Implement getDevRequires() method.
    }

    /**
     * Returns a set of package names and reasons why they are useful in
     * combination with this package.
     *
     * @return array An array of package suggestions with descriptions
     */
    public function getSuggests() {
        // TODO: Implement getSuggests() method.
    }

    /**
     * Returns an associative array of autoloading rules
     *
     * {"<type>": {"<namespace": "<directory>"}}
     *
     * Type is either "psr-4", "psr-0", "classmap" or "files". Namespaces are mapped to
     * directories for autoloading using the type specified.
     *
     * @return array Mapping of autoloading rules
     */
    public function getAutoload() {
        // TODO: Implement getAutoload() method.
    }

    /**
     * Returns an associative array of dev autoloading rules
     *
     * {"<type>": {"<namespace": "<directory>"}}
     *
     * Type is either "psr-4", "psr-0", "classmap" or "files". Namespaces are mapped to
     * directories for autoloading using the type specified.
     *
     * @return array Mapping of dev autoloading rules
     */
    public function getDevAutoload() {
        // TODO: Implement getDevAutoload() method.
    }

    /**
     * Returns a list of directories which should get added to PHP's
     * include path.
     *
     * @return array
     */
    public function getIncludePaths() {
        // TODO: Implement getIncludePaths() method.
    }

    /**
     * Stores a reference to the repository that owns the package
     *
     * @param RepositoryInterface $repository
     */
    public function setRepository(RepositoryInterface $repository) {
        // TODO: Implement setRepository() method.
    }

    /**
     * Returns a reference to the repository that owns the package
     *
     * @return RepositoryInterface
     */
    public function getRepository() {
        // TODO: Implement getRepository() method.
    }

    /**
     * Returns the package binaries
     *
     * @return array
     */
    public function getBinaries() {
        // TODO: Implement getBinaries() method.
    }

    /**
     * Returns package unique name, constructed from name and version.
     *
     * @return string
     */
    public function getUniqueName() {
        // TODO: Implement getUniqueName() method.
    }

    /**
     * Returns the package notification url
     *
     * @return string
     */
    public function getNotificationUrl() {
        // TODO: Implement getNotificationUrl() method.
    }

    /**
     * Converts the package into a readable and unique string
     *
     * @return string
     */
    public function __toString() {
        // TODO: Implement __toString() method.
    }

    /**
     * Converts the package into a pretty readable string
     *
     * @return string
     */
    public function getPrettyString() {
        // TODO: Implement getPrettyString() method.
    }

    /**
     * Returns a list of patterns to exclude from package archives
     *
     * @return array
     */
    public function getArchiveExcludes() {
        // TODO: Implement getArchiveExcludes() method.
    }

    /**
     * Returns a list of options to download package dist files
     *
     * @return array
     */
    public function getTransportOptions() {
        // TODO: Implement getTransportOptions() method.
}}