<?
require_once "Mustache.php";

// The example template
$template=file_get_contents('test.mustache');

// (we are loading it from a mustache file...)
/* $template = 'Hello {{name}}
 * You have just won ${{value}}!
 * {{#in_ca}}
 * Well, ${{taxed_value}}, after taxes.
 * {{/in_ca}}';
 */

// Data structure backing the template
class Test extends Mustache {
   public $name = "Chris";
   public $value = 10000;

   public function taxed_value() {
      return $this->value - ($this->value * 0.4);
   }

   public $in_ca = true;
}

// Mustache class provides the render($string) method
$t = new Test;
echo $t->render($template);

// Generic class example:
/** 
 * $chris = new Chris;
 * $m = new Mustache;
// render can be instantiated on any class
// without extending the mustache type
 * echo $m->render($template, $chris);
 */

?>
