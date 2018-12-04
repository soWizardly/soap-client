<?php


namespace Khatfield\SoapClient\Header;


class PackageVersionHeader extends AbstractHeaderOption
{
    protected $allowed_methods = [
        'convertLead',
        'create',
        'delete',
        'describeGlobal',
        'describeLayout',
        'describeSObject',
        'describeSObjects',
        'describeSoftphoneLayout',
        'describeTabs',
        'merge',
        'process',
        'query',
        'retrieve',
        'search',
        'undelete',
        'update',
        'upsert',
    ];

    protected $package_versions = [];

    public function __construct($package_versions = [])
    {
        $this->package_versions = $package_versions;
    }

    /**
     * @param string $namespace
     *
     * @return \SoapHeader|null
     */
    public function getHeader($namespace)
    {
        $return      = null;

        $header_data = ['PackageVersions' => []];
        foreach($this->package_versions as $package_version) {
            $header_data['PackageVersions'][] = [
                'majorNumber' => $package_version->getMajorNumber(),
                'minorNumber' => $package_version->getMinorNumber(),
                'namespace'   => $package_version->getNamespace(),
            ];
        }

        if(!empty($header_data['PackageVersions'])) {
            $return = new \SoapHeader($namespace, 'PackageVersionHeader', $header_data);
        }

        return $return;
    }
}