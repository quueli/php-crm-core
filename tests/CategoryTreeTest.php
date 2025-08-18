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
        self::assertSame('Root / Mid / Leaf', $leaf->getPathString());
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
        self::assertTrue($child->isLeaf());
    }

    public function testDescendantsDepthFirst(): void
    {
        $root = $this->cat('Root');
        $a = $this->cat('A');
        $b = $this->cat('B');
        $a1 = $this->cat('A1');
        $root->addChild($a);
        $root->addChild($b);
        $a->addChild($a1);

        $names = array_map(fn (Category $c) => $c->getName(), $root->getDescendants());
        self::assertSame(['A', 'A1', 'B'], $names);
    }

    public function testIsDescendantOf(): void
    {
        $root = $this->cat('Root');
        $mid = $this->cat('Mid');
        $leaf = $this->cat('Leaf');
        $root->addChild($mid);
        $mid->addChild($leaf);

        self::assertTrue($leaf->isDescendantOf($root));
        self::assertTrue($leaf->isDescendantOf($mid));
        self::assertFalse($root->isDescendantOf($leaf));
    }

    public function testRemoveChildClearsParent(): void
    {
        $root = $this->cat('Root');
        $child = $this->cat('Child');
        $root->addChild($child);
        $root->removeChild($child);

        self::assertNull($child->getParent());
        self::assertFalse($root->getChildren()->contains($child));
    }
}
