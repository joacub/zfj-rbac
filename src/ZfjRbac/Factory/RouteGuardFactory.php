<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace ZfjRbac\Factory;

use Interop\Container\ContainerInterface;
use Tracy\Debugger;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfjRbac\Guard\RouteGuard;

/**
 * Create a route guard
 *
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @license MIT
 */
class RouteGuardFactory implements FactoryInterface
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * {@inheritDoc}
     */
    public function setCreationOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var \ZfjRbac\Options\ModuleOptions $moduleOptions */
        $moduleOptions = $container->get('ZfjRbac\Options\ModuleOptions');
        $this->setCreationOptions($options);

        /* @var \ZfjRbac\Service\RoleService $roleService */
        $roleService = $container->get('ZfjRbac\Service\RoleService');

        $routeGuard = new RouteGuard($roleService, $this->options);
        $routeGuard->setProtectionPolicy($moduleOptions->getProtectionPolicy());

        return $routeGuard;
    }
}
