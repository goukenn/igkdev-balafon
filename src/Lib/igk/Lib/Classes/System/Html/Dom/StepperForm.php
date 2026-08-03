<?php
// @author: C.A.D. BONDJE DOUE
// @file: StepperForm.php
// @date: 20260726 14:57:26 


use IGK\Helper\Activator;
use IGK\System\Html\Dom\StepperFormDefinition;
use function igk_html_host as _h;

if (!function_exists('igk_html_node_stepperForm')) {
    /**
     * 
     * @param mixed $param 
     * @return mixed 
     */
    function igk_html_node_stepperForm($param, $options = null)
    {
        $is_closure = ($param instanceof Closure) || is_callable($param);
        $n = _h('div.igk-stepper-form'); //gk_create_notagnode();
        $n->add(_h('div.header'));
        $n->add(_h('div.route'));

        $options = array_merge(['previewText' => __('go back'), 'submit_text' => __('submit')], (array)$options ?? []);

        if ($is_closure) {
        } else if (is_array($param)) {
            $step = 1;
            foreach ($param as $k => $def) {
                /**
                 * @var StepperFormDefinition
                 */
                $stepperdef = Activator::CreateNewInstance(StepperFormDefinition::class, $def);

                $sec = $n->add(_h('section.step'));
                $sec->setAttributes([
                    'data-step' => $step
                ]);
                $sec->h2()->setClass('step__title')->content = $stepperdef->title ?? __($k);
                if ($hint = $stepperdef->hint) {
                    $sec->div()->setClass('step__hint')->content = $hint;
                }

                if ($stepperdef->fields) {
                    $sec->fields($stepperdef->fields);
                }
                if ($stepperdef->matchValidityFields) {
                    $sec["data-match-validity"] = json_encode($stepperdef->matchValidityFields);
                }
                $step++;
            }
            $n->actionbar()->host(function ($n) use ($options) {
                list($previewText, $submit_text) = igk_extract($options, 'previewText|submit_text');
                $n->button()
                    ->setAttributes([
                        'class' => 'previous'
                    ])
                    ->content = $previewText;
                $n->button()
                    ->setAttributes([
                        'class' => 'btn-primary primary',
                        'data-submit-text' => $submit_text,
                        'aria-label' => __('Continue to next step or submit the form')
                    ])
                    ->content  = __('Next');
            });
        }
        return $n;
    }
}
