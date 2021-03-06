<?php

namespace Illuminate\Routing;

use Illuminate\Support\Str;

class ResourceRegistrar
{
    /**
     * The router instance.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * The default actions for a resourceful controller.
     *
     * @var string[]
     */
    protected $resourceDefaults = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];

    /**
     * The parameters set for this resource instance.
     *
     * @var array|string
     */
    protected $parameters;

    /**
     * The global parameter mapping.
     *
     * @var array
     */
    protected static $parameterMap = [];

    /**
     * Singular global parameters.
     *
     * @var bool
     */
    protected static $singularParameters = true;

    /**
     * The verbs used in the resource URIs.
     *
     * @var array
     */
    protected static $verbs = [
        'create' => 'create',
        'edit' => 'edit',
    ];

    /**
     * Create a new resource registrar instance.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Route a resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array  $options
     * @return \Illuminate\Routing\RouteCollection
     */
    public function register($name, $controller, array $options = [])
    {
        if (isset($options['parameters']) && ! isset($this->parameters)) {
            $this->parameters = $options['parameters'];
        }

        // If the resource name contains a slash, we will assume the developer wishes to
        // register these resource routes with a prefix so we will set that up out of
        // the box so they don't have to mess with it. Otherwise, we will continue.
        if (Str::contains($name, '/')) {
            $this->prefixedResource($name, $controller, $options);

            return;
        }

        // We need to extract the base resource from the resource name. Nested resources
        // are supported in the framework, but we need to know what name to use for a
        // place-holder on the route parameters, which should be the base resources.
        $base = $this->getResourceWildcard(last(explode('.', $name)));

        $defaults = $this->resourceDefaults;

        $collection = new RouteCollection;

        foreach ($this->getResourceMethods($defaults, $options) as $m) {
            $route = $this->{'addResource'.ucfirst($m)}(
                $name, $base, $controller, $options
            );

            if (isset($options['bindingFields'])) {
                $this->setResourceBindingFields($route, $options['bindingFields']);
            }

            $collection->add($route);
        }

        return $collection;
    }

    /**
     * Build a set of prefixed resource routes.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array  $options
     * @return void
     */
    protected function prefixedResource($name, $controller, array $options)
    {
        [$name, $prefix] = $this->getResourcePrefix($name);

        // We need to extract the base resource from the resource name. Nested resources
        // are supported in the framework, but we need to know what name to use for a
        // place-holder on the route parameters, which should be the base resources.
        $callback = function ($me) use ($name, $controller, $options) {
            $me->resource($name, $controller, $options);
        };

        return $this->router->group(compact('prefix'), $callback);
    }

    /**
     * Extract the resource and prefix from a resource name.
     *
     * @param  string  $name
     * @return array
     */
    protected function getResourcePrefix($name)
    {
        $segments = explode('/', $name);

        // To get the prefix, we will take all of the name segments and implode them on
        // a slash. This will generate a proper URI prefix for us. Then we take this
    t i o n - l 1 - 2 - 1           a p i - m s - w i n - c o r e - l o c a l i z a t i o n - o b s o l e t e - l 1 - 2 - 0         a p i - m s - w i n - c o r e - p r o c e s s t h r e a d s - l 1 - 1 - 2       a p i - m s - w i n - c o r e - s t r i n g - l 1 - 1 - 0       a p i - m s - w i n - c o r e - s y s i n f o - l 1 - 2 - 1     a p i - m s - w i n - c o r e - w i n r t - l 1 - 1 - 0         a p i - m s - w i n - c o r e - x s t a t e - l 2 - 1 - 0       a p i - m s - w i n - r t c o r e - n t u s e r - w i n d o w - l 1 - 1 - 0     a p i - m s - w i n - s e c u r i t y - s y s t e m f u n c t i o n s - l 1 - 1 - 0             e x t - m s - w i n - k e r n e l 3 2 - p a c k a g e - c u r r e n t - l 1 - 1 - 0             e x t - m s - w i n - n t u s e r - d i a l o g b o x - l 1 - 1 - 0             e x t - m s - w i n - n t u s e r - w i n d o w s t a t i o n - l 1 - 1 - 0     a d v a p i 3 2         u s e r 3 2            AreFileApisANSI       CompareStringEx                                GetCurrentPackageId                 LCMapStringEx         LocaleNameToLCID    
   RoInitialize    
       RoUninitialize  INF inf NAN nan NAN(SNAN)       nan(snan)       NAN(IND)        nan(ind)    e+000               ?5@   ?5@   ?5@   ?5@   ?5@   ?5@   ?5@   ?5@   ?5@   ?5@   ?5@   ?5@   ?5@    6@   6@   6@   6@   6@   6@    6@   $6@   (6@   ,6@   06@   46@   86@   @6@   H6@   T6@   \6@   6@   d6@   l6@   t6@   ?6@   ?6@   ?6@   ?6@   ?6@   ?6@   ?6@   ?6@   ?6@          ?6@    7@   7@   7@   7@    7@   (7@   07@   @7@   P7@   `7@   x7@   ?7@   ?7@   ?7@   ?7@   ?7@   ?7@   ?7@   ?7@   ?7@   ?7@   ?7@    8@   8@   8@   8@   (8@   @8@   P8@   ?7@   `8@   p8@   ?8@   ?8@   ?8@   ?8@   ?8@   ?8@   ?8@   ?8@   9@   89@   P9@   Sun Mon Tue Wed Thu Fri Sat Sunday  Monday      Tuesday Wednesday       Thursday    Friday      Saturday    Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec     January February    March   April   June    July    August      September       October November        December    AM  PM      MM/dd/yy        dddd, MMMM dd, yyyy     HH:mm:ss        S u n   M o n   T u e   W e d   T h u   F r i   S a t   S u n d a y     M o n d a y     T u e s d a y   W e d n e s d a y       T h u r s d a y         F r i d a y     S a t u r d a y         J a n   F e b   M a r   A p r   M a y   J u n   J u l   A u g   S e p   O c t   N o v   D e c   J a n u a r y   F e b r u a r y         M a r c h       A p r i l       J u n e         J u l y         A u g u s t     S e p t e m b e r       O c t o b e r   N o v e m b e r         D e c e m b e r     A M     P M         M M / d d / y y         d d d d ,   M M M M   d d ,   y y y y   H H : m m : s s         e n - U S       ????   :   Y   w   ?   ?   ?   ?     /  M  l      ????   ;   Z   x   ?   ?   ?   ?     0  N  m                                                                                                                                                                                                                                                                                        ( ( ( ( (                                     H                ? ? ? ? ? ? ? ? ? ?        ? ? ? ? ? ?                           ? ? ? ? ? ?                                                                                                                                                                                                                                                                                           ???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????? 	
 !"#$%&'()*+,-./0123456789:;<=>?@abcdefghijklmnopqrstuvwxyz[\]^_`abcdefghijklmnopqrstuvwxyz{|}~???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????? 	
 !"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`ABCDEFGHIJKLMNOPQRSTUVWXYZ{|}~????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????0A@   @A@   XA@   xA@   ?A@   ?A@   ?A@   ?A@   ?A@   B@   (B@   @B@   hB@   ?B@   ?B@   ?B@   ?B@   ?B@   ?B@   ?B@   ?B@   C@   C@   0C@   PC@   hC@   ?B@   ?C@   ?C@   ?C@   ?C@   ?C@   ?C@    D@   D@   ?B@   (D@   ?B@   HD@   `D@   xD@   ?D@   ?D@   ?B@   No error        Operation not permitted No such file or directory       No such process Interrupted function call       Input/output error      No such device or address       Arg list too long       Exec format error       Bad file descriptor     No child processes      Resource temporarily unavailable        Not enough space        Permission denied       Bad address     Unknown error   Resource device File exists     Improper link   No such device  Not a directory Is a directory  Invalid argument        Too many open files in system   Too many open files     Inappropriate I/O control operation     File too large  No space left on device Invalid seek    Read-only file system   Too many links  Broken pipe     Domain error    Result too large        Resource deadlock avoided       Filename too long       No locks available      Function not implemented        Directory not empty     Illegal byte sequence   +       ?D@    E@   E@    E@   j a - J P       z h - C N       k o - K R       z h - T W   u k                        ?S@          ?S@          ?S@          ?S@          ?S@          ?S@          ?S@          ?S@   	       ?S@   
       ?S@          ?S@          ?S@          ?S@          ?S@          ?S@           T@          T@          T@          T@           T@          (T@          0T@          8T@          @T@          HT@          PT@          XT@          `T@          hT@          pT@           xT@   !       ?T@   "       ,E@   #       ?T@   $       ?T@   %       ?T@   &       ?T@   '       ?T@   )       ?T@   *       ?T@   +       ?T@   ,       ?T@   -       ?T@   /       ?T@   6       ?T@   7       ?T@   8       ?T@   9       ?T@   >        U@   ?       U@   @       U@   A       U@   C        U@   D       (U@   F       0U@   G       8U@   I       @U@   J       HU@   K       PU@   N       XU@   O       `U@   P       hU@   V       pU@   W       xU@   Z       ?U@   e       ?U@          ?U@         ?U@         ?U@         ?U@          E@         ?U@         ?U@         ?U@         ?U@   	      P9@         V@         V@         (V@         8V@         HV@         XV@         ?D@         E@         hV@         xV@         ?V@         ?V@         ?V@         ?V@         ?V@         ?V@         ?V@         ?V@         W@         W@          (W@   !      8W@   "      HW@   #      XW@   $      hW@   %      xW@   &      ?W@   '      ?W@   )      ?W@   *      ?W@   +      ?W@   ,      ?W@   -      ?W@   /       X@   2      X@   4       X@   5      0X@   6      @X@   7      PX@   8      `X@   9      pX@   :      ?X@   ;      ?X@   >      ?X@   ?      ?X@   @      ?X@   A      ?X@   C      ?X@   D      ?X@   E      Y@   F      Y@   G      (Y@   I      8Y@   J      HY@   K      XY@   L      hY@   N      xY@   O      ?Y@   P      ?Y@   R      ?Y@   V      ?Y@   W      ?Y@   Z      ?Y@   e      ?Y@   k      ?Y@   l      Z@   ?      Z@         (Z@          E@         8Z@   	      HZ@   
      XZ@         hZ@         xZ@         ?Z@         ?Z@         ?Z@         ?Z@         ?Z@   ,      ?Z@   ;      ?Z@   >      [@   C      [@   k      0[@         @[@         P[@         `[@   	      p[@   
      ?[@         ?[@         ?[@   ;      ?[@   k      ?[@         ?[@         ?[@         ?[@   	      \@   
      \@         (\@         8\@   ;      H\@         X\@         h\@         x\@   	      ?\@   
      ?\@         ?\@         ?\@   ;      ?\@         ?\@   	      ?\@   
       ]@         ]@          ]@   ;      8]@         H]@   	      X]@   
      h]@         x]@   ;      ?]@          ?]@   	       ?]@   
       ?]@   ;       ?]@   $      ?]@   	$      ?]@   
$       ^@   ;$      ^@   (       ^@   	(      0^@   
(      @^@   ,      P^@   	,      `^@   
,      p^@   0      ?^@   	0      ?^@   
0      ?^@   4      ?^@   	4      ?^@   
4      ?^@   8      ?^@   
8      ?^@   <       _@   
<      _@   @       _@   
@      0_@   
D      @_@   
H      P_@   
L      `_@   
P      p_@   |      ?_@   |      ?_@   a r     b g     c a     z h - C H S     c s     d a     d e     e l     e n     e s     f i     f r     h e     h u     i s     i t     j a     k o     n l     n o     p l     p t     r o     r u     h r     s k     s q     s v     t h     t r     u r     i d     b e     s l     e t     l v     l t     f a     v i     h y     a z     e u     m k     a f     k a     f o     h i     m s     k k     k y     s w     u z     t t     p a     g u     t a     t e     k n     m r     s a     m n     g l     k o k   s y r   d i v           a r - S A       b g - B G       c a - E S       c s - C Z       d a - D K       d e - D E       e l - G R       f i - F I       f r - F R       h e - I L       h u - H U       i s - I S       i t - I T       n l - N L       n b - N O       p l - P L       p t - B R       r o - R O       r u - R U       h r - H R       s k - S K       s q - A L       s v - S E       t h - T H       t r - T R       u r - P K       i d - I D       u k - U A       b e - B Y       s l - S I       e t - E E       l v - L V       l t - L T       f a - I R       v i - V N       h y - A M       a z - A Z - L a t n     e u - E S       m k - M K       t n - Z A       x h - Z A       z u - Z A       a f - Z A      