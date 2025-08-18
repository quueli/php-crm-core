<?php

namespace App\Tests;

use App\Entity\Category;
use PHPUnit\Framework\TestCase;

class CategoryTreeTest extends TestCase
{
    private function cat(string $name): Category
    {
        return (new Category())->setName($name);
    }

    public function testPathRunsRootToSelf(): void
    {
        $root = $this->cat('Root');
        $mid = $this->cat('Mid');
        $leaf = $this->cat('Leaf');
        $root->addChild($mid);
        $mid->addChild($leaf);

        $names = array_map(fn (Category $c) => $c->getName(), $leaf->getPath());
        self::assertSame(['Root', 'Mid', 'Leaf'], $names);
    }

    public function testAddChildSetsParent(): void
    {
        $root = $this->cat('Root');
        $child = $this->cat('Child');
        $root->addChild($child);

        self::assertSame($root, $child->getParent());
        self::assertTrue($root->getChildren()->contains($child));
    }

    public function testLeafDetection(): void
    {
        $root = $this->cat('Root');
        $child = $this->cat('Child');
        self::assertTrue($root->isLeaf());
        $root->addChild($child);
        self::assertFalse($root->isLeaf());
    }
}
