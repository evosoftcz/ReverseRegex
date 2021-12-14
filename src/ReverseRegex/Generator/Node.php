<?php

declare(strict_types=1);

namespace ReverseRegex\Generator;

use ArrayAccess;
use ArrayObject;
use Closure;
use Countable;
use Iterator;
use SplObjectStorage;

/**
 *  Base to all Generator Scopes.
 *
 *  @author Lewis Dyer <getintouch@icomefromthenet.com>
 *
 *  @since 0.0.1
 */
class Node implements ArrayAccess, Countable, Iterator
{
    /**
     *  @var string name of the node
     */
    protected string $label;

    /**
     *  @var ArrayObject container for node metadata
     */
    protected ArrayObject $attrs;

    /**
     *  @var SplObjectStorage container for node relationships
     */
    protected SplObjectStorage $links;

    /**
     *  Class Constructor.
     *
     *  @param string $label
     */
    public function __construct(string $label = 'node')
    {
        $this->attrs = new ArrayObject();
        $this->links = new SplObjectStorage();

        $this->setLabel($label);
    }

    /**
     *  Fetch the nodes label.
     *
     *  @return string the nodes label
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     *  Sets the node label.
     *
     *  @param string $label the nodes label
     */
    public function setLabel(string $label)
    {
        if (!(is_scalar($label) || null === $label)) {
            return false;
        }

        $this->label = $label;
    }

    /**
     *  Attach a node.
     *
     *  @param Node $node the node to attach
     *
     *  @return Node
     */
    public function &attach(Node $node)
    {
        $this->links->attach($node);

        return $this;
    }

    /**
     *  Detach a node.
     *
     *  @param Node $node the node to remove
     *
     *  @return Node
     */
    public function &detach(Node $node)
    {
        foreach ($this->links as $linked_node) {
            if ($linked_node == $node) {
                $this->links->detach($node);
            }
        }

        return $this;
    }

    /**
     *  Search for node in its relations.
     *
     *  @param Node $node the node to search for
     *
     *  @return bool true if found
     */
    public function contains(Node $node)
    {
        foreach ($this->links as $linked_node) {
            if ($linked_node == $node) {
                return true;
            }
        }

        return false;
    }

    /**
     *  Apply a closure to all relations.
     *
     * @param Closure $function
     */
    public function map(Closure $function): void
    {
        foreach ($this->links as $node) {
            $function($node);
        }
    }

    //------------------------------------------------------------------
    // Countable

    public function count(): int
    {
        return count($this->links);
    }

    //------------------------------------------------------------------
    // Iterator

    public function current(): object
    {
        return $this->links->current();
    }

    public function key(): int
    {
        return $this->links->key();
    }

    public function next(): void
    {
        $this->links->next();
    }

    public function rewind(): void
    {
        $this->links->rewind();
    }

    public function valid(): bool
    {
        return $this->links->valid();
    }

    //------------------------------------------------------------------
    // ArrayAccess Implementation

    public function offsetGet(mixed $offset): mixed
    {
        return $this->attrs->offsetGet($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->attrs->offsetSet($offset, $value);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->attrs->offsetExists($offset);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->attrs->offsetUnset($offset);
    }
}

/* End of Class */
