<?php
/**
 * @author Huafu Gandon <huafu.gandon@gmail.com>
 * @since 2016-10-20
 */

namespace Huafu\Components\Html;


/**
 * Class Element
 * @package Huafu\Components\Html
 *
 * @method static static create(null|string $tag = NULL, null|string|array $attributes = NULL, null|mixed $content = NULL)
 */
class Element extends CoreElement
{
  /** @var string[] */
  static public $lonely_tags = array('img', 'hr', 'link');

  /** @var Node[] */
  protected $_children = array();

  /**
   * @param null|string $tag
   * @param null|string|array $attributes
   * @param null|mixed $content
   */
  protected function _construct( $tag = NULL, $attributes = NULL, $content = NULL )
  {
    parent::_construct($tag, $attributes);

    if ( func_num_args() > 2 )
    {
      $this->_children = $content === NULL ? NULL : self::_content_to_nodes($content);
    }
    else
    {
      $this->_children = in_array($this->tag, self::$lonely_tags, TRUE) ? NULL : array();
    }
  }

  /**
   * @param null|mixed $content
   * @return Node|string
   */
  public function set_html_content( $content = NULL )
  {
    $this->_children = self::_content_to_nodes($content, TRUE);

    return $this;
  }

  /**
   * @param bool $from_toString
   * @return null|string
   */
  public function get_html_content( $from_toString = FALSE )
  {
    return $this->_children === NULL ? NULL : implode('', $this->_children);
  }

  /**
   * @param null|string $text
   * @return $this
   */
  public function set_text_content( $text )
  {
    $this->_children = array(Text::create($text));

    return $this;
  }

  /**
   * @param mixed $content
   * @return $this
   */
  public function append_html( $content )
  {
    $content         = self::_content_to_nodes($content, TRUE);
    $this->_children = $this->_children ? array_merge($this->_children, $content) : $content;

    return $this;
  }

  /**
   * @param mixed $content
   * @return $this
   */
  public function prepend_html( $content )
  {
    $content         = self::_content_to_nodes($content, TRUE);
    $this->_children = $this->_children ? array_merge($content, $this->_children) : $content;

    return $this;
  }

  /**
   * @param mixed $content
   * @return $this
   */
  public function append_text( $content )
  {
    $content         = self::_text_to_nodes($content);
    $this->_children = $this->_children ? array_merge($this->_children, $content) : $content;

    return $this;
  }

  /**
   * @param mixed $content
   * @return $this
   */
  public function prepend_text( $content )
  {
    $content         = self::_text_to_nodes($content);
    $this->_children = $this->_children ? array_merge($content, $this->_children) : $content;

    return $this;
  }


  /**
   * @param mixed $content
   * @param null $text_is_html
   * @return Node[]
   */
  static private function _content_to_nodes( $content, $text_is_html = NULL )
  {
    if ( !is_array($content) ) $content = array($content);
    $nodes = array();
    foreach ( $content as $node )
    {
      if ( $node === NULL || $node === '' ) continue;
      if ( is_object($node) )
      {
        if ( $node instanceof Node )
        {
          $nodes[] = $node;
        }
        else if ( self::_is_html($node) )
        {
          $nodes[] = Source::create($node);
        }
        else
        {
          $nodes[] = Text::create($node);
        }
      }
      else
      {
        $nodes[] = $text_is_html ? Source::create($node) : Text::create($node);
      }
    }

    return $nodes;
  }


  /**
   * @param mixed $text
   * @return Node[]
   */
  static private function _text_to_nodes( $text )
  {
    if ( !is_array($text) ) $text = array($text);
    $nodes = array();
    foreach ( $text as $node )
    {
      if ( $node === NULL || $node === '' ) continue;
      if ( is_object($node) && $node instanceof Text )
      {
        $nodes[] = $node;
      }
      else
      {
        $nodes[] = Text::create($node);
      }
    }

    return $nodes;
  }


  /**
   * @param mixed $data
   * @return bool
   */
  static private function _is_html( $data )
  {
    return is_object($data) && !in_array(get_class($data), self::$text_object_classes, TRUE);
  }
}