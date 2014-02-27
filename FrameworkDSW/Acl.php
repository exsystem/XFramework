<?php
/**
 * \FrameworkDSW\Acl
 * @author  许子健
 * @version $Id$
 * @since   separate file since reversion 1
 */
namespace FrameworkDSW\Acl;

use FrameworkDSW\Containers\IList;
use FrameworkDSW\Containers\TLinkedList;
use FrameworkDSW\Containers\TList;
use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Containers\TPair;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\Reflection\TClass;
use FrameworkDSW\System\EException;
use FrameworkDSW\System\EInvalidParameter;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\System\TObject;
use FrameworkDSW\Utilities\TType;

/**
 * \FrameworkDSW\Acl\EAclException
 *
 * @author 许子健
 */
class EAclException extends EException {
}

/**
 * \FrameworkDSW\Acl\EResourceExisted
 *
 * @author 许子健
 */
class EResourceExisted extends EAclException {
}

/**
 * \FrameworkDSW\Acl\ENoSuchParentResource
 *
 * @author 许子健
 */
class ENoSuchParentResource extends EAclException {
}

/**
 * \FrameworkDSW\Acl\ERoleExisted
 *
 * @author 许子健
 */
class ERoleExisted extends EAclException {
}

/**
 * \FrameworkDSW\Acl\ENoSuchParentRole
 *
 * @author 许子健
 */
class ENoSuchParentRole extends EAclException {
}

/**
 * \FrameworkDSW\Acl\ENoSuchRole
 *
 * @author 许子健
 */
class ENoSuchRole extends EAclException {
}

/**
 * \FrameworkDSW\Acl\ENoSuchResource
 *
 * @author 许子健
 */
class ENoSuchResource extends EAclException {
}

/**
 * \FrameworkDSW\Acl\IRole
 *
 * @author 许子健
 */
interface IRole extends IInterface {

    /**
     * descHere
     *
     * @return string
     */
    public function getRoleId();
}

/**
 * \FrameworkDSW\Acl\IResource
 *
 * @author 许子健
 */
interface IResource extends IInterface {

    /**
     * descHere
     *
     * @return string
     */
    public function getResourceId();
}

/**
 * \FrameworkDSW\Acl\IAssertion
 *
 * @author 许子健
 */
interface IAssertion extends IInterface {

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\TAcl $Acl
     * @param \FrameworkDSW\Acl\IRole $Role
     * @param \FrameworkDSW\Acl\IResource $Resource
     * @param string $Privilege
     * @return boolean
     */
    public static function Assert($Acl, $Role, $Resource, $Privilege);
}

/**
 * \FrameworkDSW\Acl\IStorage
 *
 * @author 许子健
 */
interface IStorage extends IInterface {

    /**
     * descHere
     *
     * @param string $Resource
     * @param string $Parent
     */
    public function AddResource($Resource, $Parent = '');

    /**
     * descHere
     *
     * @param string $Role
     * @param string $Parent
     */
    public function AddRole($Role, $Parent = '');

    /**
     * descHere
     *
     * @param string $Role
     * @param string $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion>
     */
    public function Allow($Role = '', $Resource = '', $Privilege = '', $Assertion = null);

    /**
     * descHere
     *
     * @param string $Role
     * @param string $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion>
     */
    public function Deny($Role = '', $Resource = '', $Privilege = '', $Assertion = null);

    /**
     * descHere
     *
     * @param string $Resource
     * @return \FrameworkDSW\Containers\IMap <K: string, V: string[]>
     */
    public function getResources($Resource = '');

    /**
     * descHere
     *
     * @param string $Role
     * @return \FrameworkDSW\Containers\IMap <K: string, V: string[]>
     */
    public function getRoles($Role = '');

    /**
     * descHere
     *
     * @param string $Resource
     * @return boolean
     */
    public function HasResource($Resource);

    /**
     * descHere
     *
     * @param string $Role
     * @return boolean
     */
    public function HasRole($Role);

    /**
     * descHere
     *
     * @param string $Role
     * @param string $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion>
     * @return boolean
     */
    public function IsAllowed($Role, $Resource = '', $Privilege = '', $Assertion = null);

    /**
     * descHere
     *
     * @param string $Resource
     */
    public function RemoveResource($Resource = '');

    /**
     * descHere
     *
     * @param string $Role
     */
    public function RemoveRole($Role = '');

    /**
     * descHere
     *
     * @param string $Resource
     * @param string $From
     * @param boolean $Directly
     * @return boolean
     */
    public function ResourceInheritsFrom($Resource, $From = '', $Directly = false);

    /**
     * descHere
     *
     * @param string $Role
     * @param string $From
     * @param boolean $Directly
     * @return boolean
     */
    public function RoleInheritsFrom($Role, $From = '', $Directly = false);
}

/**
 * \FrameworkDSW\Acl\TRole
 *
 * @author 许子健
 */
class TRole extends TObject implements IRole {

    /**
     *
     * @var string
     */
    private $FId = '';

    /**
     * descHere
     *
     * @param string $Id
     */
    public function __construct($Id) {
        parent::__construct();
        TType::String($Id);

        $this->FId = $Id;
    }

    /**
     * descHere
     *
     * @return string
     */
    public function getRoleId() {
        return $this->FId;
    }

}

/**
 * \FrameworkDSW\Acl\TResource
 *
 * @author 许子健
 */
class TResource extends TObject implements IResource {
    /**
     *
     * @var string
     */
    private $FId = '';

    /**
     * descHere
     *
     * @param string $Id
     */
    public function __construct($Id) {
        parent::__construct();
        TType::String($Id);

        $this->FId = $Id;
    }

    /**
     * descHere
     *
     * @return string
     */
    public function getResourceId() {
        return $this->FId;
    }

}

/**
 * \FrameworkDSW\Acl\TRuntimeStorage
 *
 * @author 许子健
 */
class TRuntimeStorage extends TObject implements IStorage {

    /**
     *
     * @var \FrameworkDSW\Containers\TMap <K: string, V: string[]>
     */
    private $FResources = null;
    /**
     *
     * @var \FrameworkDSW\Containers\TMap <K: string, V: string[]>
     */
    private $FRoles = null;
    /**
     *
     * @var \FrameworkDSW\Containers\TMap <K: \FrameworkDSW\Containers\TPair<K: string, V: string>, V: \FrameworkDSW\Containers\TList<T: \FrameworkDSW\Containers\TPair<K: string, V: \FrameworkDSW\Reflection\TClass<T: \FrameworkDSW\Acl\IAssertion>>>>
     */
    private $FRules = null;

    /**
     *
     * @param string $Role
     * @param string $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion>
     */
    private function DoAllow($Role, $Resource, $Privilege = '', $Assertion = null) {
        $mRoleResourcePair        = new TPair();
        $mRoleResourcePair->Key   = $Resource;
        $mRoleResourcePair->Value = $Role;

        $mPrivilegePair        = new TPair();
        $mPrivilegePair->Key   = $Privilege;
        $mPrivilegePair->Value = $Assertion;

        /** @noinspection PhpParamsInspection */
        if ($this->FRules->ContainsKey($mRoleResourcePair)) {
            /** @noinspection PhpIllegalArrayKeyTypeInspection */
            if (!$this->FRules[$mRoleResourcePair]->Contains($mPrivilegePair)) {
                /** @noinspection PhpIllegalArrayKeyTypeInspection */
                $this->FRules[$mRoleResourcePair]->Add($mPrivilegePair);
            }
        }
        else {
            TList::PrepareGeneric([
                'T' => [
                    TPair::class => ['K' => Framework::String, 'V' => [TClass::class => ['T' => IAssertion::class]]]]]);
            $mRule = new TList();
            /** @noinspection PhpParamsInspection */
            $mRule->Add($mPrivilegePair);
            /** @noinspection PhpParamsInspection */
            $this->FRules->Put($mRoleResourcePair, $mRule);
        }
    }

    /**
     *
     * @param string $Role
     * @param string $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion>
     */
    private function DoDeny($Role, $Resource, $Privilege = '', $Assertion = null) {
        $mRoleResourcePair        = new TPair();
        $mRoleResourcePair->Key   = $Resource;
        $mRoleResourcePair->Value = $Role;
        if ($this->FRules->ContainsKey($mRoleResourcePair)) {
            $mPrivilegePair        = new TPair();
            $mPrivilegePair->Key   = $Privilege;
            $mPrivilegePair->Value = $Assertion;
            /** @noinspection PhpIllegalArrayKeyTypeInspection */
            if ($this->FRules[$mRoleResourcePair]->Contains($mPrivilegePair)) {
                /** @noinspection PhpIllegalArrayKeyTypeInspection */
                $this->FRules[$mRoleResourcePair]->Remove($mPrivilegePair);
            }
        }
    }

    /**
     * descHere
     */
    public function __construct() {
        parent::__construct();

        TMap::PrepareGeneric(['K' => Framework::String, 'V' => Framework::String . '[]']);
        $this->FResources = new TMap();
        TMap::PrepareGeneric(['K' => Framework::String, 'V' => Framework::String . '[]']);
        $this->FRoles = new TMap();
        TMap::PrepareGeneric([
            'K' => [TPair::class => ['K' => Framework::String, 'V' => Framework::String]],
            'V' => [
                TList::class => [
                    'T' => [
                        TPair::class => ['K' => Framework::String, 'V' => [TClass::class => ['T' => IAssertion::class]]]]]]]);
        $this->FRules = new TMap();
    }

    /**
     * descHere
     */
    public function Destroy() {
        Framework::Free($this->FResources);
        Framework::Free($this->FRoles);
        Framework::Free($this->FRules);

        parent::Destroy();
    }

    /**
     * descHere
     *
     * @param string $Resource
     * @param string $Parent
     */
    public function AddResource($Resource, $Parent = '') {
        TType::String($Resource);
        TType::String($Parent);

        $mParentPath = [];
        if ($Parent != '') {
            $mParentPath   = $this->FResources[$Parent];
            $mParentPath[] = $Parent;
        }
        $this->FResources->Put($Resource, $mParentPath);
    }

    /**
     * descHere
     *
     * @param string $Role
     * @param string $Parent
     */
    public function AddRole($Role, $Parent = '') {
        TType::String($Role);
        TType::String($Parent);

        $mParentPath = [];
        if ($Parent != '') {
            $mParentPath   = $this->FResources[$Parent];
            $mParentPath[] = $Parent;
        }
        $this->FRoles->Put($Role, $mParentPath);
    }

    /**
     * descHere
     *
     * @param string $Role
     * @param string $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion>
     */
    public function Allow($Role = '', $Resource = '', $Privilege = '', $Assertion = null) {
        TType::String($Role);
        TType::String($Resource);
        TType::String($Privilege);

        if ($Role != '' && $Resource != '') {
            $this->DoAllow($Role, $Resource, $Privilege, $Assertion);
        }
        elseif ($Role == '' && $Resource != '') {
            foreach ($this->FRoles as $mRole => $mPath) {
                $this->DoAllow($mRole, $Resource, $Privilege, $Assertion);
            }
        }
        elseif ($Role != '' && $Resource == '') {
            foreach ($this->FResources as $mResource => $mPath) {
                $this->DoAllow($Role, $mResource, $Privilege, $Assertion);
            }
        }
        elseif ($Role == '' && $Resource == '') {
            foreach ($this->FRoles as $mRole => $mRolePath) {
                /** @noinspection PhpUnusedLocalVariableInspection */
                foreach ($this->FResources as $mResource => &$mResourcePath) {
                    $this->DoAllow($mRole, $mResource, $Privilege, $Assertion);
                }
            }
        }
    }

    /**
     * descHere
     *
     * @param string $Role
     * @param string $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion>
     */
    public function Deny($Role = '', $Resource = '', $Privilege = '', $Assertion = null) {
        TType::String($Role);
        TType::String($Resource);
        TType::String($Privilege);

        if ($Role != '' && $Resource != '') {
            $this->DoDeny($Role, $Resource, $Privilege, $Assertion);
        }
        elseif ($Role == '' && $Resource != '') {
            foreach ($this->FRoles as $mRole => $mPath) {
                $this->DoDeny($mRole, $Resource, $Privilege, $Assertion);
            }
        }
        elseif ($Role != '' && $Resource == '') {
            foreach ($this->FResources as $mResource => $mPath) {
                $this->DoDeny($Role, $mResource, $Privilege, $Assertion);
            }
        }
        elseif ($Role == '' && $Resource == '') {
            foreach ($this->FRoles as $mRole => $mRolePath) {
                /** @noinspection PhpUnusedLocalVariableInspection */
                foreach ($this->FResources as $mResource => &$mResourcePath) {
                    $this->DoDeny($mRole, $mResource, $Privilege, $Assertion);
                }
            }
        }
    }

    /**
     * descHere
     *
     * @param string $Resource
     * @return \FrameworkDSW\Containers\IMap <K: string, V: string[]>
     */
    public function getResources($Resource = '') {
        TType::String($Resource);

        if ($Resource == '') {
            return $this->FResources;
        }
        else {
            TMap::PrepareGeneric(['K' => Framework::String, 'V' => Framework::String . '[]']);
            $mResult = new TMap();
            foreach ($this->FResources as $mResource => $mPath) {
                if (in_array($Resource, $mPath, true)) {
                    $mResult->Put($mResource, $mPath);
                }
            }

            return $mResult;
        }
    }

    /**
     * descHere
     *
     * @param string $Role
     * @return \FrameworkDSW\Containers\IMap <K: string, V: string[]>
     */
    public function getRoles($Role = '') {
        TType::String($Role);

        if ($Role == '') {
            return $this->FRoles;
        }
        else {
            TMap::PrepareGeneric(['K' => Framework::String, 'V' => Framework::String]);
            $mResult = new TMap();
            foreach ($this->FRoles as $mRole => $mPath) {
                if (in_array($Role, $mPath, true)) {
                    $mResult->Put($mRole, $mPath);
                }
            }

            return $mResult;
        }
    }

    /**
     * descHere
     *
     * @param string $Resource
     * @return boolean
     */
    public function HasResource($Resource) {
        TType::String($Resource);

        return $this->FResources->ContainsKey($Resource);
    }

    /**
     * descHere
     *
     * @param string $Role
     * @return boolean
     */
    public function HasRole($Role) {
        TType::String($Role);

        return $this->FRoles->ContainsKey($Role);
    }

    /**
     * descHere
     *
     * @param string $Role
     * @param string $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion>
     * @return boolean
     */
    public function IsAllowed($Role, $Resource = '', $Privilege = '', $Assertion = null) {
        TType::String($Role);
        TType::String($Resource);
        TType::String($Privilege);

        $mResourceRolePair        = new TPair();
        $mResourceRolePair->Key   = $Resource;
        $mResourceRolePair->Value = $Role;

        $mPrivilegePair        = new TPair();
        $mPrivilegePair->Key   = $Privilege;
        $mPrivilegePair->Value = $Assertion;

        if (!$this->FRules->ContainsKey($mResourceRolePair)) {
            foreach ($this->FResources[$Resource] as $mAncestorResource) {
                $mResourceRolePair->Key = $mAncestorResource;
                /** @noinspection PhpIllegalArrayKeyTypeInspection */
                if (($this->FRules->ContainsKey($mResourceRolePair)) && ($this->FRules[$mResourceRolePair]->Contains($mPrivilegePair))) {
                    return true;
                }
            }

            return false;
        }
        else {
            /** @noinspection PhpIllegalArrayKeyTypeInspection */
            if ($this->FRules[$mResourceRolePair]->Contains($mPrivilegePair)) {
                return true;
            }
        }

        return false;
    }

    /**
     * descHere
     *
     * @param string $Resource
     */
    public function RemoveResource($Resource = '') {
        TType::String($Resource);
        if ($Resource == '') {
            $this->FResources->Clear();
            $this->FRules->Clear();
        }
        else {
            TLinkedList::PrepareGeneric(['T' => Framework::String]);
            $mPendingResources = new TLinkedList();

            foreach ($this->FResources as $mResource => &$mPath) {
                if (in_array($Resource, $mPath, true)) {
                    $this->FResources->Delete($mResource);
                    $mPendingResources->Add($Resource);
                }
            }

            foreach ($this->FRules as $mResourceRolePair => $mPrivilegePair) {
                if ($mPendingResources->Contains($mResourceRolePair->Value)) {
                    $this->FRules->Remove($this->FRules[$mResourceRolePair]);
                }
            }
        }
    }

    /**
     * descHere
     *
     * @param string $Role
     */
    public function RemoveRole($Role = '') {
        TType::String($Role);
        if ($Role == '') {
            $this->FRoles->Clear();
            $this->FRules->Clear();
        }
        else {
            TLinkedList::PrepareGeneric(['T' => Framework::String]);
            $mPendingRoles = new TLinkedList();

            foreach ($this->FRoles as $mRole => &$mPath) {
                if (in_array($Role, $mPath, true)) {
                    $this->FResources->Delete($mRole);
                    $mPendingRoles->Add($mRole);
                }
            }

            foreach ($this->FRules as $mResourceRolePair => $mPrivilegePair) {
                if ($mPendingRoles->Contains($mResourceRolePair->Key)) {
                    $this->FRules->Remove($this->FRules[$mResourceRolePair]);
                }
            }

            Framework::Free($mPendingRoles);
        }
    }

    /**
     * descHere
     *
     * @param string $Resource
     * @param string $From
     * @param boolean $Directly
     * @return boolean
     */
    public function ResourceInheritsFrom($Resource, $From = '', $Directly = false) {
        TType::String($Resource);
        TType::String($From);
        TType::Bool($Directly);

        $mPath = $this->FResources[$Resource];
        if ($From == '' && $mPath === []) {
            return true;
        }
        if ($From != '' && $Directly == true && $From == $mPath[count($mPath) - 1]) {
            return true;
        }
        if ($From != '' && $Directly == false && in_array($From, $mPath, true)) {
            return true;
        }

        return false;
    }

    /**
     * descHere
     *
     * @param string $Role
     * @param string $From
     * @param boolean $Directly
     * @return boolean
     */
    public function RoleInheritsFrom($Role, $From = '', $Directly = false) {
        TType::String($Role);
        TType::String($From);
        TType::Bool($Directly);

        $mPath = $this->FRoles[$Role];
        if ($From == '' && $mPath === []) {
            return true;
        }
        if ($From != '' && $Directly == true && $From == $mPath[count($mPath) - 1]) {
            return true;
        }
        if ($From != '' && $Directly == false && in_array($From, $mPath, true)) {
            return true;
        }

        return false;
    }

}

/**
 * \FrameworkDSW\Acl\TAcl
 *
 * @author 许子健
 */
class TAcl extends TObject {

    /**
     *
     * @var \FrameworkDSW\Acl\IStorage
     */
    private $FStorage = null;

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IStorage $Storage
     */
    public function __construct($Storage = null) {
        parent::__construct();
        TType::Object($Storage, IStorage::class);

        $this->FStorage = $Storage;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IResource $Resource
     * @param \FrameworkDSW\Acl\IResource $Parent
     * @throws EResourceExisted
     * @throws ENoSuchParentResource
     * @return TAcl
     */
    public function AddResource($Resource, $Parent = null) {
        TType::Object($Resource, IResource::class);
        TType::Object($Parent, IResource::class);

        $mResourceId = $Resource->getResourceId();
        $mParentId   = '';
        if ($Parent != null) {
            if ($this->FStorage->HasResource($mParentId)) {
                throw new ENoSuchParentResource(sprintf('No such parent resource: %s.', $Parent->getResourceId()));
            }
            $mParentId = $Parent->getResourceId();
        }
        if ($this->FStorage->HasResource($mResourceId)) {
            throw new EResourceExisted(sprintf('Resource existed: %s.', $mResourceId));
        }

        $this->FStorage->AddResource($mResourceId, $mParentId);

        return $this;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IRole $Role
     * @param \FrameworkDSW\Acl\IRole $Parent
     * @throws ERoleExisted
     * @throws ENoSuchParentRole
     * @return TAcl
     */
    public function AddRole($Role, $Parent = null) {
        TType::Object($Role, IRole::class);
        TType::Object($Parent, IRole::class);

        $mRoleId   = $Role->getRoleId();
        $mParentId = '';
        if ($Parent != null) {
            if ($this->FStorage->HasRole($mParentId)) {
                throw new ENoSuchParentRole(sprintf('No such parent role: %s.', $Parent->getRoleId()));
            }
            $mParentId = $Parent->getRoleId();
        }
        if ($this->FStorage->HasRole($mRoleId)) {
            throw new ERoleExisted(sprintf('Role existed: %s.', $mRoleId));
        }

        $this->FStorage->AddRole($mRoleId, $mParentId);

        return $this;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IRole $Role
     * @param \FrameworkDSW\Acl\IResource $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion>
     * @throws ENoSuchRole
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return TAcl
     */
    public function Allow($Role = null, $Resource = null, $Privilege = '', $Assertion = null) {
        TType::Object($Role, IRole::class);
        TType::Object($Resource, IResource::class);
        TType::String($Privilege);
        TType::Object($Assertion, [TClass::class => ['T' => IAssertion::class]]);

        if ($Privilege == '' && $Assertion === null) {
            throw new EInvalidParameter(sprintf('Invalid parameter: empty $Privilege and null $Assertion appeared at same time.'));
        }
        if ($Privilege != '' && $Assertion !== null) {
            throw new EInvalidParameter(sprintf('Invalid parameter: non-empty $Privilege and non-null $Assertion appeared at same time.'));
        }

        $mResourceId = '';
        if ($Resource != null) {
            $mResourceId = $Resource->getResourceId();
        }
        if ($Role == null) {
            $mRoles = $this->FStorage->getRoles();
            /** @noinspection PhpUnusedLocalVariableInspection */
            foreach ($mRoles as $mRoleId => &$mPath) {
                $this->FStorage->Allow($mRoleId, $mResourceId, $Privilege, $Assertion);
            }
            Framework::Free($mRoles);
        }
        else {
            $mRoleId = $Role->getRoleId();
            if (!$this->FStorage->HasRole($mRoleId)) {
                throw new ENoSuchRole(sprintf('No such role: %s.', $mRoleId));
            }
            $this->FStorage->Allow($mRoleId, $mResourceId, $Privilege, $Assertion);
        }

        return $this;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IRole $Role
     * @param \FrameworkDSW\Acl\IResource $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion>
     * @throws ENoSuchRole
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return TAcl
     */
    public function Deny($Role = null, $Resource = null, $Privilege = '', $Assertion = null) {
        TType::Object($Role, IRole::class);
        TType::Object($Resource, IResource::class);
        TType::String($Privilege);

        if ($Privilege == '' && $Assertion === null) {
            throw new EInvalidParameter(sprintf('Invalid parameter: empty $Privilege and null $Assertion appeared at same time.'));
        }
        if ($Privilege != '' && $Assertion !== null) {
            throw new EInvalidParameter(sprintf('Invalid parameter: non-empty $Privilege and non-null $Assertion appeared at same time.'));
        }

        $mResourceId = '';
        if ($Resource == null) {
            $mResourceId = $Resource->getResourceId();
        }
        if ($Role == null) {
            $mRoles = $this->FStorage->getRoles();
            /** @noinspection PhpUnusedLocalVariableInspection */
            foreach ($mRoles as $mRoleId => &$mPath) {
                $this->FStorage->Deny($mRoleId, $mResourceId, $Privilege, $Assertion);
            }
            Framework::Free($mRoles);
        }
        else {
            $mRoleId = $Role->getRoleId();
            if (!$this->FStorage->HasRole($mRoleId)) {
                throw new ENoSuchRole(sprintf('No such role: %s.', $mRoleId));
            }
            $this->FStorage->Deny($mRoleId, $mResourceId, $Privilege, $Assertion);
        }

        return $this;
    }

    /**
     * descHere
     *
     * @param string $ResourceId
     * @throws ENoSuchResource
     * @return \FrameworkDSW\Acl\IResource
     */
    public function getResourceById($ResourceId) {
        TType::String($ResourceId);

        if ($this->FStorage->HasResource($ResourceId)) {
            return new TResource($ResourceId);
        }
        else {
            throw new ENoSuchResource(sprintf('No such resource: %s.', $ResourceId));
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IResource $Resource
     * @throws ENoSuchResource
     * @return \FrameworkDSW\Containers\IMap <K: \FrameworkDSW\Acl\IResource, V: \FrameworkDSW\Containers\IList<T: \FrameworkDSW\Acl\IResource>>
     */
    public function getResources($Resource = null) {
        TType::Object($Resource, IResource::class);

        $mResourceId = '';
        if ($Resource != null) {
            $mResourceId = $Resource->getResourceId();
            if (!$this->FStorage->HasResource($mResourceId)) {
                throw new ENoSuchResource(sprintf('No such resource: %s.', $mResourceId));
            }
        }
        $mRaw = $this->FStorage->getResources($mResourceId);
        TMap::PrepareGeneric(['K' => IResource::class, 'V' => [IList::class => ['T' => IResource::class]]]);
        $mResult = new TMap(true);
        foreach ($mRaw as $mRawResource => $mRawPath) {
            TList::PrepareGeneric(['T' => TResource::class]);
            $mPath = new TList(count($mRawPath), true);
            foreach ($mRawPath as $mRawPathElement) {
                $mPath->Add(new TResource($mRawPathElement));
            }
            $mResult->Put(new TResource($mRawResource), $mPath);
        }
        Framework::Free($mRaw);

        return $mResult;
    }

    /**
     * descHere
     *
     * @param string $ResourceId
     * @return \FrameworkDSW\Containers\IMap <K: \FrameworkDSW\Acl\IResource, V: \FrameworkDSW\Containers\IList<T: \FrameworkDSW\Acl\IResource>>
     */
    public function getResourcesById($ResourceId = '') {
        TType::String($ResourceId);

        if ($ResourceId == '') {
            return $this->getResources();
        }
        else {
            return $this->getResources($this->getResourceById($ResourceId));
        }
    }

    /**
     * descHere
     *
     * @param string $RoleId
     * @throws ENoSuchRole
     * @return \FrameworkDSW\Acl\IRole
     */
    public function getRoleById($RoleId) {
        TType::String($RoleId);

        if ($this->FStorage->HasRole($RoleId)) {
            return new TRole($RoleId);
        }
        else {
            throw new ENoSuchRole(sprintf('No such role: %s.', $RoleId));
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IRole $Role
     * @throws ENoSuchRole
     * @return \FrameworkDSW\Containers\IMap <K: \FrameworkDSW\Acl\IRole, V: IList<T: \FrameworkDSW\Acl\IRole>>
     */
    public function getRoles($Role = null) {
        TType::Object($Role, IResource::class);

        $mRoleId = '';
        if ($Role != null) {
            $mRoleId = $Role->getRoleId();
            if (!$this->FStorage->HasRole($mRoleId)) {
                throw new ENoSuchRole(sprintf('No such role: %s.', $mRoleId));
            }
        }
        $mRaw = $this->FStorage->getRoles($mRoleId);
        TMap::PrepareGeneric(['K' => IRole::class, 'V' => [IList::class => ['T' => IRole::class]]]);
        $mResult = new TMap(true);
        foreach ($mRaw as $mRawRole => $mRawPath) {
            TList::PrepareGeneric(['T' => TResource::class]);
            $mPath = new TList(count($mRawPath), true);
            foreach ($mRawPath as $mRawPathElement) {
                $mPath->Add(new TRole($mRawPathElement));
            }
            $mResult->Put(new TRole($mRawRole), $mPath);
        }
        Framework::Free($mRaw);

        return $mResult;
    }

    /**
     * descHere
     *
     * @param string $RoleId
     * @return \FrameworkDSW\Containers\IMap <K: \FrameworkDSW\Acl\IRole, V: \FrameworkDSW\Containers\IList<T: \FrameworkDSW\Acl\IRole>>
     */
    public function getRolesById($RoleId = '') {
        TType::String($RoleId);

        if ($RoleId == '') {
            return $this->getRoles();
        }
        else {
            return $this->getRoles($this->getRoleById($RoleId));
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IResource $Resource
     * @return boolean
     */
    public function HasResource($Resource) {
        TType::Object($Resource, IResource::class);

        return $this->FStorage->HasResource($Resource->getResourceId());
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IRole $Role
     * @return boolean
     */
    public function HasRole($Role) {
        TType::Object($Role, IRole::class);

        return $this->FStorage->HasRole($Role->getRoleId());
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IRole $Role
     * @param \FrameworkDSW\Acl\IResource $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion>
     * @throws ENoSuchResource
     * @throws ENoSuchRole
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return boolean
     */
    public function IsAllowed($Role = null, $Resource = null, $Privilege = '', $Assertion = null) {
        TType::Object($Role, IRole::class);
        TType::Object($Resource, IResource::class);
        TType::String($Privilege);

        if ($Privilege == '' && $Assertion === null) {
            throw new EInvalidParameter(sprintf('Invalid parameter: empty $Privilege and null $Assertion appeared at same time.'));
        }
        if ($Privilege != '' && $Assertion !== null) {
            throw new EInvalidParameter(sprintf('Invalid parameter: non-empty $Privilege and non-null $Assertion appeared at same time.'));
        }

        $mResourceId = '';
        if ($Resource != null) {
            $mResourceId = $Resource->getResourceId();
            if (!$this->FStorage->HasResource($mResourceId)) {
                throw new ENoSuchResource(sprintf('No such resource: %s.', $mResourceId));
            }
        }
        if ($Role == null) {
            $mRoles = $this->FStorage->getRoles();
            foreach ($mRoles as $mRoleId) {
                if (!$this->FStorage->IsAllowed($mRoleId, $mResourceId, $Privilege, $Assertion)) {
                    Framework::Free($mRoles);

                    return false;
                }
            }
            Framework::Free($mRoles);

            return true;
        }
        else {
            $mRoleId = $Role->getRoleId();
            if (!$this->FStorage->HasRole($mRoleId)) {
                throw new ENoSuchRole(sprintf('No such role: %s.', $mRoleId));
            }

            return $this->FStorage->IsAllowed($mRoleId, $mResourceId, $Privilege, $Assertion);
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IResource $Resource
     * @throws ENoSuchResource
     * @return \FrameworkDSW\Acl\TAcl
     */
    public function RemoveResource($Resource = null) {
        TType::Object($Resource, IResource::class);

        if ($Resource != null) {
            $mResourceId = $Resource->getResourceId();
            if ($this->FStorage->HasResource($mResourceId)) {
                $this->FStorage->RemoveResource($mResourceId);

                return $this;
            }
            else {
                throw new ENoSuchResource(sprintf('No such resource: %s.', $mResourceId));
            }
        }
        $this->FStorage->RemoveResource();

        return $this;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IRole $Role
     * @throws ENoSuchRole
     * @return \FrameworkDSW\Acl\TAcl
     */
    public function RemoveRole($Role = null) {
        TType::Object($Role, IRole::class);

        if ($Role != null) {
            $mRoleId = $Role->getRoleId();
            if ($this->FStorage->HasRole($mRoleId)) {
                $this->FStorage->RemoveRole($mRoleId);

                return $this;
            }
            else {
                throw new ENoSuchRole(sprintf('No such role: %s.', $mRoleId));
            }
        }
        $this->FStorage->RemoveRole();

        return $this;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IResource $Resource
     * @param \FrameworkDSW\Acl\IResource $From
     * @param boolean $Directly
     * @throws ENoSuchParentResource
     * @return boolean
     */
    public function ResourceInheritsFrom($Resource, $From = null, $Directly = false) {
        TType::Object($Resource, IResource::class);
        TType::Object($From, IResource::class);
        TType::Bool($Directly);

        $mResourceId = $Resource->getResourceId();
        $mFromId     = '';
        if ($From != null) {
            $mFromId = $From->getResourceId();
            if (!$this->FStorage->HasResource($mFromId)) {
                throw new ENoSuchParentResource(sprintf('No such parent resource: %s.', $mFromId));
            }
        }

        return $this->FStorage->ResourceInheritsFrom($mResourceId, $mFromId, $Directly);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IRole $Role
     * @param \FrameworkDSW\Acl\IRole $From
     * @param boolean $Directly
     * @throws ENoSuchParentRole
     * @return boolean
     */
    public function RoleInheritsFrom($Role, $From = null, $Directly = false) {
        TType::Object($Role, IRole::class);
        TType::Object($From, IRole::class);
        TType::Bool($Directly);

        $mRoleId = $Role->getRoleId();
        $mFromId = '';
        if ($From != null) {
            $mFromId = $From->getRoleId();
            if (!$this->FStorage->HasRole($mFromId)) {
                throw new ENoSuchParentRole(sprintf('No such parent role: %s.', $mFromId));
            }
        }

        return $this->FStorage->ResourceInheritsFrom($mRoleId, $mFromId, $Directly);
    }

}