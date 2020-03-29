<?php

 /* 
  * 
  * RevolveR HTML Forms
  *
  * v.1.8.0
  *
  *
  *
  *
  *
  *               ^
  *              | |
  *            @#####@
  *          (###   ###)-.
  *        .(###     ###) \
  *       /  (###   ###)   )
  *      (=-  .@#####@|_--"
  *      /\    \_|l|_/ (\
  *     (=-\     |l|    /
  *      \  \.___|l|___/
  *      /\      |_|   /
  *     (=-\._________/\
  *      \             /
  *        \._________/
  *          #  ----  #
  *          #   __   #
  *          \########/
  *
  *
  *
  * Developer: Dmitry Maltsev
  *
  * License: Apache 2.0
  *
  *
  */

final class HTMLFormBuilder {

  protected static $enc = null;

  protected static $ilanguage;
  protected static $captcha;

  function __construct( string $l ) {

    self::$ilanguage = $l;

    self::$captcha  = '<div class="revolver__captcha">';
    self::$captcha .= '<div class="revolver__captcha-td">';
    self::$captcha .= '<div class="captcha_pattern_1">';

    self::$captcha .= TRANSLATIONS[ $l ]['Pattern'];
    
    self::$captcha .= ':<div class="revolver__captcha-pattern">';
    self::$captcha .= '<canvas id="overprint" width="101" height="101"></canvas>';
    self::$captcha .= '</div>';
    self::$captcha .= '</div>';
    
    self::$captcha .= '<div class="captcha_pattern_2">';
    
    self::$captcha .= TRANSLATIONS[ $l ]['Repeat pattern'];

    self::$captcha .= ':<div class="revolver__captcha-pattern">';

    self::$captcha .= base64_decode(

        'PGRpdiBpZD0iZHJhd3BhbmUiPjxkaXYgaWQ9InBhbmUtMS0xIiBkYXRhLXNlbGVjdGVkPSJmY'.
        'WxzZSIgZGF0YS14eT0iMDowIj48L2Rpdj48ZGl2IGlkPSJwYW5lLTEtMiIgZGF0YS1zZWxlY3'.
        'RlZD0iZmFsc2UiIGRhdGEteHk9IjI1OjAiPjwvZGl2PjxkaXYgaWQ9InBhbmUtMS0zIiBkYXR'.
        'hLXNlbGVjdGVkPSJmYWxzZSIgZGF0YS14eT0iNTA6MCI+PC9kaXY+PGRpdiBpZD0icGFuZS0x'.
        'LTQiIGRhdGEtc2VsZWN0ZWQ9ImZhbHNlIiBkYXRhLXh5PSI3NTowIj48L2Rpdj48ZGl2IGlkP'.
        'SJwYW5lLTItMSIgZGF0YS1zZWxlY3RlZD0iZmFsc2UiIGRhdGEteHk9IjA6MjUiPjwvZGl2Pj'.
        'xkaXYgaWQ9InBhbmUtMi0yIiBkYXRhLXNlbGVjdGVkPSJmYWxzZSIgZGF0YS14eT0iMjU6MjU'.
        'iPjwvZGl2PjxkaXYgaWQ9InBhbmUtMi0zIiBkYXRhLXNlbGVjdGVkPSJmYWxzZSIgZGF0YS14'.
        'eT0iNTA6MjUiPjwvZGl2PjxkaXYgaWQ9InBhbmUtMi00IiBkYXRhLXNlbGVjdGVkPSJmYWxzZ'.
        'SIgZGF0YS14eT0iNzU6MjUiPjwvZGl2PjxkaXYgaWQ9InBhbmUtMy0xIiBkYXRhLXNlbGVjdG'.
        'VkPSJmYWxzZSIgZGF0YS14eT0iMDo1MCI+PC9kaXY+PGRpdiBpZD0icGFuZS0zLTIiIGRhdGE'.
        'tc2VsZWN0ZWQ9ImZhbHNlIiBkYXRhLXh5PSIyNTo1MCI+PC9kaXY+PGRpdiBpZD0icGFuZS0z'.
        'LTMiIGRhdGEtc2VsZWN0ZWQ9ImZhbHNlIiBkYXRhLXh5PSI1MDo1MCI+PC9kaXY+PGRpdiBpZ'.
        'D0icGFuZS0zLTQiIGRhdGEtc2VsZWN0ZWQ9ImZhbHNlIiBkYXRhLXh5PSI3NTo1MCI+PC9kaX'.
        'Y+PGRpdiBpZD0icGFuZS00LTEiIGRhdGEtc2VsZWN0ZWQ9ImZhbHNlIiBkYXRhLXh5PSIwOjc'.
        '1Ij48L2Rpdj48ZGl2IGlkPSJwYW5lLTQtMiIgZGF0YS1zZWxlY3RlZD0iZmFsc2UiIGRhdGEt'.
        'eHk9IjI1Ojc1Ij48L2Rpdj48ZGl2IGlkPSJwYW5lLTQtMyIgZGF0YS1zZWxlY3RlZD0iZmFsc'.
        '2UiIGRhdGEteHk9IjUwOjc1Ij48L2Rpdj48ZGl2IGlkPSJwYW5lLTQtNCIgZGF0YS1zZWxlY3'.
        'RlZD0iZmFsc2UiIGRhdGEteHk9Ijc1Ojc1Ij48L2Rpdj48L2Rpdj4='

    );

    self::$captcha .= '</div>';
    self::$captcha .= '</div>';
    self::$captcha .= '</div>';
    self::$captcha .= '</div>';

  }

  protected function __clone() {

  }

  public static function build( iterable $params, ?bool $tabs = null, $translate = null ): string {

    $translate = $translate ? $translate : TRANSLATIONS;

    $HTMLForm  = '';
    $formAttrs = '';

    // Main form attributes
    foreach( $params as $p => $pv ) {

      switch( $p ) {

        case 'id':
        case 'class':
        case 'action':
        case 'method':
        case 'enctype':
        case 'target': {

          $formAttrs .= ' '. $p .'="'. $pv .'"';

        }

        case 'fieldsets': 

          if( !$tabs ) {

            $formFieldsets = is_array( $pv ) ? self::buildFieldsets( $pv, $translate ) : $pv;

          }

          break;

        case 'encrypt':

          self::$enc = $pv ? true : null;

          break;

        case 'captcha':

            if( $pv ) {

              $formCaptcha  = '<fieldset class="revolver__captcha-wrapper">'."\n";
              $formCaptcha .= '<legend style="width: 50%">'. TRANSLATIONS[ self::$ilanguage ]['Captcha'] .':</legend>'."\n";
              $formCaptcha .= self::$captcha ."\n";
              $formCaptcha .= '<input type="hidden" name="revolver_captcha">'."\n";
              $formCaptcha .= '</fieldset>'."\n";

            }

          break;

        case 'submit': 

          $formSubmit = '<input type="submit" value="'. $translate[ self::$ilanguage ][ $pv ] .'">';

          break;

      }

    }

    if( $p === 'tabs' && $tabs ) {

      $FormTabsHeading = $formTabsContents = '';

      $tab_count = 0;

      foreach( $pv as $tbs => $t ) {

        $FormTabsHeading .= '<li class="revolver__tabs-tab-'. $tbs .' revolver__tabs-tab'. ( isset($t['active']) && (bool)$t['active'] ? ' activetab"' : '"') .' data-link="tab-'. $tab_count .'">'. $translate[ self::$ilanguage ][ $t['title'] ] .'</li>';

        $formTabsContents .= '<div data-content="tab-'. $tab_count .'" '. ( isset($t['active']) && (bool)$t['active'] ? ' class="tabactive"' : '') .'>';

        if( isset( $t['fieldsets'] ) ) {

          $formTabsContents .= self::buildFieldsets( $t['fieldsets'],  $translate );

        }

        if( isset( $t['html'] ) ) {

          $formTabsContents .= $t['html'];

        }

        $formTabsContents .= '</div>';

        $tab_count++;

      }

    }

    $HTMLForm .= "\n". '<div class="revolver__form-wrapper"><form'. $formAttrs .' accept-charset="utf-8">'. "\n";

    if( $tabs ) {

      $HTMLForm .= '<aside id="tabs" class="revolver__tabs tabs">';

      $HTMLForm .= '<ul>';
      $HTMLForm .= $FormTabsHeading;
      $HTMLForm .= '</ul>';

      $HTMLForm .= $formTabsContents;

    }

    $HTMLForm .= $formFieldsets ."\n";

    if( !$tabs ) {

      $HTMLForm .= $formCaptcha ."\n";
      $HTMLForm .= $formSubmit ."\n";

    }

    if( $tabs ) { 

      $HTMLForm .= '</aside>' . $formCaptcha . $formSubmit . "\n";

    }

    $HTMLForm .= '</form></div>'. "\n";

    return $HTMLForm;

  }

  protected static function buildFieldsets( iterable $fieldsets, iterable $translate ): string {

      $HTMLFormFieldsets = '';

      foreach( $fieldsets as $fs => $f ) {

        $collapse_class = $collapse_exapnder = $collapse_exapnder_1 = $title = '';

        if( isset( $f['collapse'] ) && $f['collapse'] && !isset( $f['no-collapse'] ) ) {

          $collapse_class      = ' class="revolver__collapse-form-legend"';
          $collapse_exapnder   = '<output class="revolver__collapse-form-contents" style="overflow: hidden; width: 0; height: 0; line-height: 0; display: inline-block;">';
          $collapse_exapnder_1 = '</output>';

        }

        if( isset( $f['title:html'] ) ) {

          $title = $f['title:html'];

        }
        else {

          $title = $translate[ self::$ilanguage ][ $f['title'] ];

        }

        $HTMLFormFieldsets .= '<fieldset id="'. $fs .'">';

        if( !isset( $f['no-legend'] ) ) {

          $HTMLFormFieldsets .= '<legend style="min-width:50%"'. $collapse_class .'>'. $title .':</legend>';          

        }

        $HTMLFormFieldsets .= $collapse_exapnder;

        $HTMLFormFieldsetsAfter = '';

        if( !isset( $f['html:contents'] ) ) {

          foreach ( self::buildLabels( $f['labels'], $translate ) as $labels ) {

            if( $labels[1] ) {

              $HTMLFormFieldsetsAfter .= $labels[0];

            }
            else {

              $HTMLFormFieldsets .= $labels[0];

            }

          }

        }
        else {

          $HTMLFormFieldsets .= $f['html:contents'];

        }

        $HTMLFormFieldsets .= $collapse_exapnder_1;
        $HTMLFormFieldsets .= $HTMLFormFieldsetsAfter;
        $HTMLFormFieldsets .= '</fieldset>';

      }

      return $HTMLFormFieldsets;

  }

  private static function buildLabels( iterable $labels, iterable $translate ): iterable {

    $AllLabels = [];

    $count = 0;

    foreach ($labels as $lb => $l) {

      $Labeltitle = ( isset( $l['title:html'] ) ? $l['title:html'] : $translate[ self::$ilanguage ][ $l['title'] ] );

      if( isset( $l['no-collapse'] ) && (int)$l['no-collapse'] === 1 ) {

        $attrs = ' id="'. $lb .'" class="revolver__collapse-form-no-collapse"';

        $flag = true;

      }
      else if( isset( $l['collapse'] ) && (int)$l['collapse'] === 1 ) {

        $HTMLFormLabelsWrap_1  = '<h4 class="revolver__collapse-form-legend">'.  $Labeltitle .'</h4>';
        $HTMLFormLabelsWrap_1 .= '<output class="revolver__collapse-form-contents" style="overflow: hidden; width: 0; height: 0; line-height: 0; display: inline-block;">';
        $HTMLFormLabelsWrap_2  = '<br /><br /></output>';

        $attrs = 'id="'. $lb .'"';

        $flag = null;

      }
      else {

        $attrs = 'id="'. $lb .'"';

        $flag = null;

      }

      $HTMLlabel = isset( $l['no-label'] ) && (int)$l['no-label'] === 1 ? self::buildFields( $l['fields'], $translate ) : '<label '. $attrs .'>'. $Labeltitle .': '. self::buildFields( $l['fields'], $translate ) .'</label>';

      $HTMLFormLabels = isset( $l['collapse'] ) && (int)$l['collapse'] === 1 ? $HTMLFormLabelsWrap_1 . self::buildFields( $l['fields'], $translate ) . $HTMLFormLabelsWrap_2 : $HTMLlabel;

      if( isset( $l['access'] ) && isset( $l['auth'] ) ) {

        if( isset( FORM_ACCESS['permissions'][ $l['access'] ] ) && (int)$l['auth'] === (int)FORM_ACCESS['auth'] ) {

          $AllLabels[ $count ] = [ $HTMLFormLabels, $flag ];

        }

        if( isset( FORM_ACCESS['permissions'][ $l['access'] ] ) && $l['auth'] === 'all' ) {

          $AllLabels[ $count ] = [ $HTMLFormLabels, $flag ];

        }

      }

      $count++;

    }

    return $AllLabels;

  }

  private static function buildFields( iterable $fields, iterable $translate ): string {

    $HTMLFormFields = $EndTagFlag = $HTMLContents = $HTMLTextareaContents = '';

    foreach( $fields as $fl => $f ) {

      foreach( $f as $input => $i ) {

        switch( $input ) {

          case 'type':

            $type = explode(':', $i);

            // Make valid tag with attrs
            switch ( $type[0] ) {

              case 'input':

                $HTMLFormFields .= '<input';

                // Choose input type
                switch ( $type[1] ) {

                  case 'password':
                  case 'hidden':
                  case 'number':
                  case 'email':
                  case 'text':
                  case 'file':
                  case 'tel':
                  case 'url':

                    $HTMLFormFields .= ' type="'. $type[1] .'"';

                    break;

                  case 'checkbox':
                  case 'radio':

                    $HTMLFormFields .= ' type="'. $type[1] .'"'. ( $type[2] === 'checked' ? ' checked="checked"' : '' );

                    break;

                } 

                $EndTagFlag = 'single';

                break;

              case 'textarea':

                $HTMLFormFields .= '<textarea ';

                $EndTagFlag = '</textarea>';

                break;

              case 'select':

                $HTMLFormFields .= '<select ';

                $EndTagFlag = '</select>';

                break;

            }

            break;

          case 'name':

            $HTMLFormFields .= ' name="'. $i .'"';

            break;

          case 'placeholder':

            if( isset( $translate[ self::$ilanguage ][ $i ] ) ) {

              $HTMLFormFields .= ' placeholder="'. $translate[ self::$ilanguage ][ $i ] .'"';

            }

            break;

          case 'required':

            if( (int)$i === 1 ) {

               $HTMLFormFields .= ' required="required"';

            }

            break;

          case 'readonly':

            if( (int)$i === 1 ) {

               $HTMLFormFields .= ' readonly="readonly"';

            }

          case 'multiple':

            if( (int)$i === 1 ) {

               $HTMLFormFields .= ' multiple="multiple"';

            }

            break;

          case 'disabled':

            if( (int)$i === 1 ) {

               $HTMLFormFields .= ' disabled="disabled"';

            }

            break;

          case 'value':

            $HTMLFormFields .= (bool)strlen( $i ) ? ' value="'. $i .'"' : '';

            break;

          case 'rows':

            $HTMLFormFields .= (bool)strlen( $i ) ? ' rows="'. $i .'"' : '';

            break;

          case 'html:contents':

            $HTMLContents .= $i;

            break;

          case 'value:html':

            $HTMLTextareaContents = $i;

            break;

          case 'id':

            $HTMLFormFields .= ' id="'. $i .'"';

            break;

        }

      }

      if( (bool)strlen( $HTMLContents ) ) {

        $HTMLFormFields = $HTMLContents;

      }
      else {

        $HTMLFormFields .= ( $EndTagFlag === 'single' ? ' />' : '>'. $HTMLTextareaContents . $EndTagFlag );

      }

    }

    return $HTMLFormFields;

  }

}

?>
