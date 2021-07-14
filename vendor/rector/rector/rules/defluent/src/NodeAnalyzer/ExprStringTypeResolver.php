<?php

declare(strict_types=1);

namespace Rector\Defluent\NodeAnalyzer;

use PhpParser\Node\Expr;
use PHPStan\Type\TypeWithClassName;
use PHPStan\Type\UnionType;
use Rector\NodeTypeResolver\NodeTypeResolver;
use Rector\PHPStan\Type\AliasedObjectType;
use Rector\PHPStanStaticTypeMapper\Utils\TypeUnwrapper;

final class ExprStringTypeResolver
{
    /**
     * @var NodeTypeResolver
     */
    private $nodeTypeResolver;

    /**
     * @var TypeUnwrapper
     */
    private $typeUnwrapper;

    public function __construct(NodeTypeResolver $nodeTypeResolver, TypeUnwrapper $typeUnwrapper)
    {
        $this->nodeTypeResolver = $nodeTypeResolver;
        $this->typeUnwrapper = $typeUnwrapper;
    }

    public function resolve(Expr $expr): ?string
    {
        $exprStaticType = $this->nodeTypeResolver->getStaticType($expr);
        if ($exprStaticType instanceof UnionType) {
            $exprStaticType = $this->typeUnwrapper->unwrapNullableType($exprStaticType);
        }

        if (! $exprStaticType instanceof TypeWithClassName) {
            // nothing we can do, unless
            return null;
        }

        if ($exprStaticType instanceof AliasedObjectType) {
            return $exprStaticType->getFullyQualifiedClass();
        }

        return $exprStaticType->getClassName();
    }
}
