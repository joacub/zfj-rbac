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

namespace ZfjRbacTest\Guard;

use Zend\ServiceManager\ServiceManager;
use ZfjRbac\Guard\GuardPluginManager;
use ZfjRbac\Options\ModuleOptions;

/**
 * @covers \ZfjRbac\Guard\GuardPluginManager
 */
class GuardPluginManagerTest extends \PHPUnit_Framework_TestCase
{
    public function guardProvider()
    {
        return [
            [
                'ZfjRbac\Guard\RouteGuard',
                [
                    'admin/*' => 'foo'
                ]
            ],
            [
                'ZfjRbac\Guard\RoutePermissionsGuard',
                [
                    'post/delete' => 'post.delete'
                ]
            ],
            [
                'ZfjRbac\Guard\ControllerGuard',
                [
                    [
                        'controller' => 'Foo',
                        'actions'    => 'bar',
                        'roles'      => 'baz'
                    ]
                ]
            ],
            [
                'ZfjRbac\Guard\ControllerPermissionsGuard',
                [
                    [
                        'controller'  => 'Foo',
                        'actions'     => 'bar',
                        'permissions' => 'baz'
                    ]
                ]
            ],
        ];
    }

    /**
     * @dataProvider guardProvider
     */
    public function testCanCreateDefaultGuards($type, $options)
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService('ZfjRbac\Options\ModuleOptions', new ModuleOptions());
        $serviceManager->setService(
            'ZfjRbac\Service\RoleService',
            $this->getMock('ZfjRbac\Service\RoleService', [], [], '', false)
        );
        $serviceManager->setService(
            'ZfjRbac\Service\AuthorizationService',
            $this->getMock('ZfjRbac\Service\AuthorizationService', [], [], '', false)
        );

        $pluginManager = new GuardPluginManager();
        $pluginManager->setServiceLocator($serviceManager);

        $guard = $pluginManager->get($type, $options);

        $this->assertInstanceOf($type, $guard);
    }

    public function testThrowExceptionForInvalidPlugin()
    {
        $this->setExpectedException('ZfjRbac\Exception\RuntimeException');

        $pluginManager = new GuardPluginManager();
        $pluginManager->get('stdClass');
    }
}
