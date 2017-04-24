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
use ZfjRbac\Exception;
use ZfjRbac\Role\ObjectRepositoryRoleProvider;

/**
 * Factory used to create an object repository role provider
 *
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @license MIT
 */
class ObjectRepositoryRoleProviderFactory implements FactoryInterface
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
     * {@inheritDoc}
     * @return ObjectRepositoryRoleProvider
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $objectRepository = null;

        $this->setCreationOptions($options);
        if (!isset($this->options['role_name_property'])) {
            throw new Exception\RuntimeException('The "role_name_property" option is missing');
        }

        if (isset($this->options['object_repository'])) {
            /* @var \Doctrine\Common\Persistence\ObjectRepository $objectRepository */
            $objectRepository = $container->get($this->options['object_repository']);

            return new ObjectRepositoryRoleProvider($objectRepository, $this->options['role_name_property']);
        }

        if (isset($this->options['object_manager']) && isset($this->options['class_name'])) {
            /* @var \Doctrine\Common\Persistence\ObjectManager $objectManager */
            $objectManager    = $container->get($this->options['object_manager']);
            $objectRepository = $objectManager->getRepository($this->options['class_name']);

            return new ObjectRepositoryRoleProvider($objectRepository, $this->options['role_name_property']);
        }

        throw new Exception\RuntimeException(
            'No object repository was found while creating the ZfjRbac object repository role provider. Are
             you sure you specified either the "object_repository" option or "object_manager"/"class_name" options?'
        );
    }
}