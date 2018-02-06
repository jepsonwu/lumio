<?php

namespace Dingo\Api\Http\Parser;

use Illuminate\Http\Request;
use Dingo\Api\Contract\Http\Parser;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AcceptPrefix extends Accept
{
    /**
     * Standards tree.
     *
     * @var string
     */
    protected $standardsTree;

    /**
     * API subtype.
     *
     * @var string
     */
    protected $subtype;

    /**
     * Default version.
     *
     * @var string
     */
    protected $version;

    /**
     * Default format.
     *
     * @var string
     */
    protected $format;


    protected $versionPattern;

    /**
     * Create a new accept parser instance.
     *
     * @param string $standardsTree
     * @param string $subtype
     * @param string $version
     * @param string $format
     * @param string $versionPattern
     *
     * @return void
     */
    public function __construct($standardsTree, $subtype, $version, $format, $versionPattern)
    {
        $this->standardsTree = $standardsTree;
        $this->subtype = $subtype;
        $this->version = $version;
        $this->format = $format;
        $this->versionPattern = $versionPattern;
    }

    /**
     * Parse the accept header on the incoming request. If strict is enabled
     * then the accept header must be available and must be a valid match.
     *
     * @param \Illuminate\Http\Request $request
     * @param bool $strict
     *
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     *
     * @return array
     */
    public function parse(Request $request, $strict = false)
    {
        $path = '/' . trim($request->path(), '/');
        $preg = '/' . $this->versionPattern . '/';
        preg_match($preg, $path, $m);
        $pattern = '/application\/' . $this->standardsTree . '\.(' . $this->subtype . ')\.([\w\d\.\-]+)\+([\w]+)/';
        if (!preg_match($pattern, $request->header('accept'), $matches)) {
            if ($strict) {
                throw new BadRequestHttpException('Accept header could not be properly parsed because of a strict matching process.');
            }
            $default = 'application/' . $this->standardsTree . '.' . $this->subtype . '.' . $this->version . '+' . $this->format;
            preg_match($pattern, $default, $matches);
        }
        if (isset($m['version']) && $m['version']) {
            $matches[2] = $m['version'];
        }
        return array_combine(['subtype', 'version', 'format'], array_slice($matches, 1));
    }
}
