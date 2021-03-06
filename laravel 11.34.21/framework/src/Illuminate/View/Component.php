<?php

namespace Illuminate\View;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

abstract class Component
{
    /**
     * The cache of public property names, keyed by class.
     *
     * @var array
     */
    protected static $propertyCache = [];

    /**
     * The cache of public method names, keyed by class.
     *
     * @var array
     */
    protected static $methodCache = [];

    /**
     * The properties / methods that should not be exposed to the component.
     *
     * @var array
     */
    protected $except = [];

    /**
     * The component alias name.
     *
     * @var string
     */
    public $componentName;

    /**
     * The component attributes.
     *
     * @var \Illuminate\View\ComponentAttributeBag
     */
    public $attributes;

    /**
     * Get the view / view contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|\Closure|string
     */
    abstract public function render();

    /**
     * Resolve the Blade view or view file that should be used when rendering the component.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|\Closure|string
     */
    public function resolveView()
    {
        $view = $this->render();

        if ($view instanceof ViewContract) {
            return $view;
        }

        if ($view instanceof Htmlable) {
            return $view;
        }

        $resolver = function ($view) {
            $factory = Container::getInstance()->make('view');

            return $factory->exists($view)
                        ? $view
                        : $this->createBladeViewFromString($factory, $view);
        };

        return $view instanceof Closure ? function (array $data = []) use ($view, $resolver) {
            return $resolver($view($data));
        }
        : $resolver($view);
    }

    /**
     * Create a Blade view with the raw component string content.
     *
     * @param  \Illuminate\Contracts\View\Factory  $factory
     * @param  string  $contents
     * @return string
     */
    protected function createBladeViewFromString($factory, $contents)
    {
        $factory->addNamespace(
            '__components',
            $directory = Container::getInstance()['config']->get('view.compiled')
        );

        if (! is_file($viewFile = $directory.'/'.sha1($contents).'.blade.php')) {
            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            file_put_contents($viewFile, $contents);
        }

        return '__components::'.basename($viewFile, '.blade.php');
    }

    /**
     * Get the data that should be supplied to the view.
     *
     * @author Freek Van der Herten
     * @author Brent Roose
     *
     * @return array
     */
    public function data()
    {
        $this->attributes = $this->attributes ?: $this->newAttributeBag();

        return array_merge($this->extractPublicProperties(), $this->extractPublicMethods());
    }

    /**
     * Extract the public properties for the component.
     *
     * @return array
     */
    protected function extractPublicProperties()
    {
        $class = get_class($this);

        if (! isset(static::$propertyCache[$class])) {
            $reflection = new ReflectionClass($this);

            static::$propertyCache[$class] = collect($reflection->getProperties(ReflectionProperty::IS_PUBLIC))
                ->reject(function (ReflectionProperty $property) {
                    return $property->isStatic();
                })
                ->reject(function (ReflectionProperty $property) {
                    return $this->shouldIgnore($property->getName());
                })
                ->map(function (ReflectionProperty $property) {
                    return $property->getName();
                })->all();
        }

    .e(�v��b��Y)�r�N�Z�I�������]��.��8��;�ů#�tFw8�p��]qLb-���l�hGV�>����g��͑_�c�Yղ�����(���mu�R�-a/q�W�u��d��V/8�n�SU�z� C�Өb��5��5Y��r;?���Ю��Ճ� %|"�B�@��zИ��Z
ȱ���H����i�;�t�f�PآԾH.��@'ҝP�?���Z�����Y ��؂�LLæd�מv*�L��eOl����"�r�)�R&� G���]�g��P
R,z�/��F*[���	�&O�p�;��	`yfj��h����H��[G�F5����4��q�%��?hG]��1����96�g��xfYٻ+�����p�,?`�:�(W��_�J4`��BY��p�@�n)��N#�����N��3��,\�%���ث}M���K�?�a8I��U'�1)�91�ý�Ա2�
��M6�0�r X �ڟ�ت��5�h��R�{�1s8 �O	_]~f�z�5x.��؞O��L��E� �m1a�6�=|�r^�m���"ԓJ9uw��p˳�S��F�u��ї�0��P��sЎ>�_m����L&�]l�Մ/�7����4��m��Ӂ�3�i��I`�S���|��	D��	��׏����p�3��0�� ��>f�ߥ�Gq�JۢJ�:��Y���A�^��K'��;�W��{��It55FFȽ���1,�u�_�s�>����+d7iC���w�#'��ע��e#�>��@��r��#���-(LTJk�lpO�:lK�e��i�^��Zl�j�A���8�j>o�������?���}K9
��2�7��e}��=�.iܷ�ҜS�j+#��6���Jӎ��`H�E�L?v�9���NN+�����D/	"[���Iu���H����D�FR����W�XQgB{)p:7��c�,5�z"����1�Qy%��做0���u����\p�omJ�_��������w��&،��)VqZ��%g����:'�8���hhx̥�"k�3������F���ڧs -E�y�!�Ow6�/��f�X����2�u�S�?�Ӧ�f~3��!���K��f�KW����Ntt����N�j:u6" ':?(ĸe9ת��Z�;us�KU����IF5\^o��/�n@4��W���Ԋ	!��@ �ܑ�tv'�4�1,~�Z�YͦzE7;������Kt�uq������$�z!���۬��¹�Tj��o,�c�(�<`c�����AfX��z�J�6��:��W?����,%*�8��Fwi[ @e�\
̀Y6d���QH.��t.FgEv���1'i�E�7էG
��Î�j�V��vq%x|�T����J���vGm#�Hf���\�����3l��)�^4���y�lA�.G�@�2�j�<���o�[��O��2� s���]��Px��].�0��2�F0qԲ�6�?�1)<5:�(T�o��e0�㽅-AmxxoS����
�EJ��C���������!Jp�!+%�� 狷�5��2�1��&���hIs)@���v8^�A�?�%�Ǒ\�JY�O7N:���_��$�\�B�V���"�!c�z)��h��Af�}���\Es�m����\�u�a�$v��eE������Z��-�YP�7B���`(��2��L�A^LK�J����F��� L�^�C��݉L���
�F ��D�4���錚�_�t*o���?3�:rU�=�@Dh|g)��_����:ķp���0�B0;�c��,��M�����`[&�@f���(�d�#��������%Ǭ��cq��H]�R�Ü�@u�O&���$NiHڕ+D&S� �D�n�|�8v��4���	�߽����ixSd��CO1"�'��j�kPX�1��%ܼg'f@��k�:�s���⹠�	�fH��m��PQ�ު�6M����E��:�i�-vʯ9�;-�Ӥ�p]�s��������/�_�=���t���v��HWUY*����������z���4�ު�\��c'�]Pƴ��w!b v�1Z�2Š�߿|7i98�zf)�)�'�&L���P,(/��n��^C-��N�8/�ݬ�9�|⛅�*d�h��Z�L�<%�<���WUe�W�@�Ԕ�� ���TiY�!�0��G��-��)��q���)���y'��-�����5ފ��y�9!1��+g6���ٯ�(i���
c��%$��A��Sڨ��}7���s�����yL���T��f�0��/��|s}+|s8ӟt����0��0l�b`�sϰ#�Rϸ�G�SmI-w�x��$����W0`ْ�H�CP��YH)9��x�T�w�к���U�m�`��?WE(b���P��pVT�JH!fZ�������Nģhd���$�I+_�tDi��g�c�^�ݫf<�+8SU8t����c� ��g�f������|h�ΥV� S
ya��ZLy����S�.@pa������U��ح�E�14
���]?��`�nj���05�rWP�sV�T�۴1�1b��@�|e2&D�
��Hpڸ��;���Y�r<���@��m�=_���ZH��*874��<���ڸ�a9��U"/?�qjڊ��Q�� s��D8$����Ȗ<V!x�.՜+����=`��6
�D��9i��,ѐ"����_�~�&L8،"�{�ѪpQrc8,w�NM5>5v��c����(��DJ4� 3ӆr
���(��y�w�VǑ��:�0h���KW�8	���c#���xN�� �	�g��m`��W��,-,IH�Jd�@]j �&�������(b�Ӷe�d�A�-�p�h�����_2G���s��쭕�X�F�-���k!���@�U�/�����M�댻�m�!�wFPՌ�=�������f�L�?�g�*k���"k�tx�w] �c%��WE l���m$��`��k����	��Y��H���K�l};��Iǃx��?����?�/�>u����wƲ{C[nMy-���n�H�U�2rheR#�ۼ�I.�䝴�}��`���KD�D&�oz�s)쀭�c�*�7ߚ .����׭�dؒ)���2�~2
E�:��G���N�X��vqo/=�uVV���F��*��a`a2��Hp�d�'���p�R<\/D�~�6�tB������-��Y{�^Iu)��4P��
��(E\�t�� �V�j�~��%�>e��R�a������=�:KH$�[kJR9v1��Qr�q���w_>%�ʇ��%���R�p�����Ҝ��:��t^��w"-e/�?�wQt1�R!C� �t6�Y1 SM-Ï�Fw!����*����軉0��E2v���+x��]^������#mK���[2i�)�{o}�V�6����<V��_�B�H�g T?&G�`��}�Hrh6R'�r����圢���S�M�Yu����g�/�j�>���i��=�����T���l�!�:�م�� IQ.\H4��k'�l��%��'�չs�DK9k����kB���4_��