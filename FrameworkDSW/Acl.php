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
 * \FrameworkDSW\Acl\EAclResourceExisted
 *
 * @author 许子健
 */
class EAclResourceExisted extends EAclException {
}

/**
 * \FrameworkDSW\Acl\EAclNoSuchParentResource
 *
 * @author 许子健
 */
class EAclNoSuchParentResource extends EAclException {
}

/**
 * \FrameworkDSW\Acl\EAclRoleExisted
 *
 * @author 许子健
 */
class EAclRoleExisted extends EAclException {
}

/**
 * \FrameworkDSW\Acl\EAclNoSuchParentRole
 *
 * @author 许子健
 */
class EAclNoSuchParentRole extends EAclException {
}

/**
 * \FrameworkDSW\Acl\EAclNoSuchRole
 *
 * @author 许子健
 */
class EAclNoSuchRole extends EAclException {
}

/**
 * \FrameworkDSW\Acl\EAclNoSuchResource
 *
 * @author 许子健
 */
class EAclNoSuchResource extends EAclException {
}

/**
 * \FrameworkDSW\Acl\IAclRole
 *
 * @author 许子健
 */
interface IAclRole extends IInterface {

    /**
     * descHere
     *
     * @return string
     */
    public function getRoleId();
}

/**
 * \FrameworkDSW\Acl\IAclResource
 *
 * @author 许子健
 */
interface IAclResource extends IInterface {

    /**
     * descHere
     *
     * @return string
     */
    public function getResourceId();
}

/**
 * \FrameworkDSW\Acl\IAclAssertion
 *
 * @author 许子健
 */
interface IAclAssertion extends IInterface {

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\TAcl $ACL
     * @param \FrameworkDSW\Acl\IAclRole $Role
     * @param \FrameworkDSW\Acl\IAclResource $Resource
     * @param string $Privilege
     * @return boolean
     */
    public static function Assert($ACL, $Role, $Resource, $Privilege);
}

/**
 * \FrameworkDSW\Acl\IAclStorage
 *
 * @author 许子健
 */
interface IAclStorage extends IInterface {

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
     * @param mixed $Assertion
     */
    public function Allow($Role = '', $Resource = '', $Privilege = '', $Assertion = null);

    /**
     * descHere
     *
     * @param string $Role
     * @param string $Resource
     * @param string $Privilege
     * @param mixed $Assertion
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
     * @param mixed $Assertion
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
 * \FrameworkDSW\Acl\TAclRole
 *
 * @author 许子健
 */
class TAclRole extends TObject implements IAclRole {

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
 * \FrameworkDSW\Acl\TAclResource
 *
 * @author 许子健
 */
class TAclResource extends TObject implements IAclResource {
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
 * \FrameworkDSW\Acl\TRuntimeAclStorage
 *
 * @author 许子健
 */
class TRuntimeAclStorage extends TObject implements IAclStorage {

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
     * @var \FrameworkDSW\Containers\TMap <K: \FrameworkDSW\Containers\TPair<K: string, V: string>, V: \FrameworkDSW\Containers\TList<T: \FrameworkDSW\Containers\TPair<K: string, V: mixed>>>
     */
    private $FRules = null;

    /**
     *
     * @param string $Role
     * @param string $Resource
     * @param string $Privilege
     * @param mixed $Assertion
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
                    TPair::class => ['K' => 'string', 'V' => null]]]);
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
     * @param mixed $Assertion
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

        TMap::PrepareGeneric(['K' => 'string', 'V' => 'array']);
        $this->FResources = new TMap();
        TMap::PrepareGeneric(['K' => 'string', 'V' => 'array']);
        $this->FRoles = new TMap();
        TMap::PrepareGeneric([
            'K' => [TPair::class => ['K' => 'string', 'V' => 'string']],
            'V' => [
                TList::class => [
                    'T' => [
                        TPair::class => ['K' => 'string', 'V' => null]]]]]);
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
     * @param mixed $Assertion
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
     * @param mixed $Assertion
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
            TMap::PrepareGeneric(['K' => 'string', 'V' => 'string']);
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
            TMap::PrepareGeneric(['K' => 'string', 'V' => 'string']);
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
     * @param mixed $Assertion
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
            TLinkedList::PrepareGeneric(['T' => 'string']);
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
            TLinkedList::PrepareGeneric(['T' => 'string']);
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
     * @var \FrameworkDSW\Acl\IAclStorage
     */
    private $FStorage = null;

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IAclStorage $Storage
     */
    public function __construct($Storage = null) {
        parent::__construct();
        TType::Object($Storage, IAclStorage::class);

        $this->FStorage = $Storage;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IAclResource $Resource
     * @param \FrameworkDSW\Acl\IAclResource $Parent
     * @throws EAclResourceExisted
     * @throws EAclNoSuchParentResource
     * @return TAcl
     */
    public function AddResource($Resource, $Parent = null) {
        TType::Object($Resource, IAclResource::class);
        TType::Object($Parent, IAclResource::class);

        $mResourceId = $Resource->getResourceId();
        $mParentId   = '';
        if ($Parent != null) {
            if ($this->FStorage->HasResource($mParentId)) {
                throw new EAclNoSuchParentResource();
            }
            $mParentId = $Parent->getResourceId();
        }
        if ($this->FStorage->HasResource($mResourceId)) {
            throw new EAclResourceExisted();
        }

        $this->FStorage->AddResource($mResourceId, $mParentId);

        return $this;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IAclRole $Role
     * @param \FrameworkDSW\Acl\IAclRole $Parent
     * @throws EAclRoleExisted
     * @throws EAclNoSuchParentRole
     * @return TAcl
     */
    public function AddRole($Role, $Parent = null) {
        TType::Object($Role, IAclRole::class);
        TType::Object($Parent, IAclRole::class);

        $mRoleId   = $Role->getRoleId();
        $mParentId = '';
        if ($Parent != null) {
            if ($this->FStorage->HasRole($mParentId)) {
                throw new EAclNoSuchParentRole();
            }
            $mParentId = $Parent->getRoleId();
        }
        if ($this->FStorage->HasRole($mRoleId)) {
            throw new EAclRoleExisted();
        }

        $this->FStorage->AddRole($mRoleId, $mParentId);

        return $this;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IAclRole $Role
     * @param \FrameworkDSW\Acl\IAclResource $Resource
     * @param string $Privilege
     * @param mixed $Assertion
     * @throws EAclNoSuchRole
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return TAcl
     */
    public function Allow($Role = null, $Resource = null, $Privilege = '', $Assertion = null) {
        TType::Object($Role, IAclRole::class);
        TType::Object($Resource, IAclResource::class);
        TType::String($Privilege);

        if ($Privilege == '' && $Assertion === null) {
            throw new EInvalidParameter();
        }
        if ($Privilege != '' && $Assertion !== null) {
            throw new EInvalidParameter();
        }
        if ($Assertion !== null) {
            $mReflection = new \ReflectionClass($Assertion);
            if (!$mReflection->implementsInterface(IAclAssertion::class)) {
                throw new EInvalidParameter();
            }
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
                throw new EAclNoSuchRole();
            }
            $this->FStorage->Allow($mRoleId, $mResourceId, $Privilege, $Assertion);
        }

        return $this;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IAclRole $Role
     * @param \FrameworkDSW\Acl\IAclResource $Resource
     * @param string $Privilege
     * @param mixed $Assertion
     * @throws EAclNoSuchRole
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return TAcl
     */
    public function Deny($Role = null, $Resource = null, $Privilege = '', $Assertion = null) {
        TType::Object($Role, IAclRole::class);
        TType::Object($Resource, IAclResource::class);
        TType::String($Privilege);

        if ($Privilege == '' && $Assertion === null) {
            throw new EInvalidParameter();
        }
        if ($Privilege != '' && $Assertion !== null) {
            throw new EInvalidParameter();
        }
        if ($Assertion !== null) {
            $mReflection = new \ReflectionClass($Assertion);
            if (!$mReflection->implementsInterface(IAclAssertion::class)) {
                throw new EInvalidParameter();
            }
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
                throw new EAclNoSuchRole();
            }
            $this->FStorage->Deny($mRoleId, $mResourceId, $Privilege, $Assertion);
        }

        return $this;
    }

    /**
     * descHere
     *
     * @param string $ResourceId
     * @throws EAclNoSuchResource
     * @return \FrameworkDSW\Acl\IAclResource
     */
    public function getResourceById($ResourceId) {
        TType::String($ResourceId);

        if ($this->FStorage->HasResource($ResourceId)) {
            return new TAclResource($ResourceId);
        }
        else {
            throw new EAclNoSuchResource();
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IAclResource $Resource
     * @throws EAclNoSuchResource
     * @return \FrameworkDSW\Containers\IMap <K: \FrameworkDSW\Acl\IAclResource, V: \FrameworkDSW\Containers\IList<T: \FrameworkDSW\Acl\IAclResource>>
     */
    public function getResources($Resource = null) {
        TType::Object($Resource, IAclResource::class);

        $mResourceId = '';
        if ($Resource != null) {
            $mResourceId = $Resource->getResourceId();
            if (!$this->FStorage->HasResource($mResourceId)) {
                throw new EAclNoSuchResource();
            }
        }
        $mRaw = $this->FStorage->getResources($mResourceId);
        TMap::PrepareGeneric(['K' => IAclResource::class, 'V' => [IList::class => ['T' => IAclResource::class]]]);
        $mResult = new TMap(true);
        foreach ($mRaw as $mRawResource => $mRawPath) {
            TList::PrepareGeneric(['T' => TAclResource::class]);
            $mPath = new TList(count($mRawPath), true);
            foreach ($mRawPath as $mRawPathElement) {
                $mPath->Add(new TAclResource($mRawPathElement));
            }
            $mResult->Put(new TAclResource($mRawResource), $mPath);
        }
        Framework::Free($mRaw);

        return $mResult;
    }

    /**
     * descHere
     *
     * @param string $ResourceId
     * @return \FrameworkDSW\Containers\IMap <K: \FrameworkDSW\Acl\IAclResource, V: \FrameworkDSW\Containers\IList<T: \FrameworkDSW\Acl\IAclResource>>
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
     * @throws EAclNoSuchRole
     * @return \FrameworkDSW\Acl\IAclRole
     */
    public function getRoleById($RoleId) {
        TType::String($RoleId);

        if ($this->FStorage->HasRole($RoleId)) {
            return new TAclRole($RoleId);
        }
        else {
            throw new EAclNoSuchRole();
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IAclRole $Role
     * @throws EAclNoSuchRole
     * @return \FrameworkDSW\Containers\IMap <K: \FrameworkDSW\Acl\IAclRole, V: IList<T: \FrameworkDSW\Acl\IAclRole>>
     */
    public function getRoles($Role = null) {
        TType::Object($Role, IAclResource::class);

        $mRoleId = '';
        if ($Role != null) {
            $mRoleId = $Role->getRoleId();
            if (!$this->FStorage->HasRole($mRoleId)) {
                throw new EAclNoSuchRole();
            }
        }
        $mRaw = $this->FStorage->getRoles($mRoleId);
        TMap::PrepareGeneric(['K' => IAclRole::class, 'V' => [IList::class => ['T' => IAclRole::class]]]);
        $mResult = new TMap(true);
        foreach ($mRaw as $mRawRole => $mRawPath) {
            TList::PrepareGeneric(['T' => TAclResource::class]);
            $mPath = new TList(count($mRawPath), true);
            foreach ($mRawPath as $mRawPathElement) {
                $mPath->Add(new TAclRole($mRawPathElement));
            }
            $mResult->Put(new TAclRole($mRawRole), $mPath);
        }
        Framework::Free($mRaw);

        return $mResult;
    }

    /**
     * descHere
     *
     * @param string $RoleId
     * @return \FrameworkDSW\Containers\IMap <K: \FrameworkDSW\Acl\IAclRole, V: \FrameworkDSW\Containers\IList<T: \FrameworkDSW\Acl\IAclRole>>
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
     * @param \FrameworkDSW\Acl\IAclResource $Resource
     * @return boolean
     */
    public function HasResource($Resource) {
        TType::Object($Resource, IAclResource::class);

        return $this->FStorage->HasResource($Resource->getResourceId());
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IAclRole $Role
     * @return boolean
     */
    public function HasRole($Role) {
        TType::Object($Role, IAclRole::class);

        return $this->FStorage->HasRole($Role->getRoleId());
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IAclRole $Role
     * @param \FrameworkDSW\Acl\IAclResource $Resource
     * @param string $Privilege
     * @param mixed $Assertion
     * @throws EAclNoSuchResource
     * @throws EAclNoSuchRole
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return boolean
     */
    public function IsAllowed($Role = null, $Resource = null, $Privilege = '', $Assertion = null) {
        TType::Object($Role, IAclRole::class);
        TType::Object($Resource, IAclResource::class);
        TType::String($Privilege);

        if ($Privilege == '' && $Assertion === null) {
            throw new EInvalidParameter();
        }
        if ($Privilege != '' && $Assertion !== null) {
            throw new EInvalidParameter();
        }
        if ($Assertion !== null) {
            $mReflection = new \ReflectionClass($Assertion);
            if (!$mReflection->implementsInterface(IAclAssertion::class)) {
                throw new EInvalidParameter();
            }
        }

        $mResourceId = '';
        if ($Resource != null) {
            $mResourceId = $Resource->getResourceId();
            if (!$this->FStorage->HasResource($mResourceId)) {
                throw new EAclNoSuchResource();
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
                throw new EAclNoSuchRole();
            }

            return $this->FStorage->IsAllowed($mRoleId, $mResourceId, $Privilege, $Assertion);
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IAclResource $Resource
     * @throws EAclNoSuchResource
     * @return \FrameworkDSW\Acl\TAcl
     */
    public function RemoveResource($Resource = null) {
        TType::Object($Resource, IAclResource::class);

        if ($Resource != null) {
            $mResourceId = $Resource->getResourceId();
            if ($this->FStorage->HasResource($mResourceId)) {
                $this->FStorage->RemoveResource($mResourceId);

                return $this;
            }
            else {
                throw new EAclNoSuchResource();
            }
        }
        $this->FStorage->RemoveResource();

        return $this;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IAclRole $Role
     * @throws EAclNoSuchRole
     * @return \FrameworkDSW\Acl\TAcl
     */
    public function RemoveRole($Role = null) {
        TType::Object($Role, IAclRole::class);

        if ($Role != null) {
            $mRoleId = $Role->getRoleId();
            if ($this->FStorage->HasRole($mRoleId)) {
                $this->FStorage->RemoveRole($mRoleId);

                return $this;
            }
            else {
                throw new EAclNoSuchRole();
            }
        }
        $this->FStorage->RemoveRole();

        return $this;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IAclResource $Resource
     * @param \FrameworkDSW\Acl\IAclResource $From
     * @param boolean $Directly
     * @throws EAclNoSuchParentResource
     * @return boolean
     */
    public function ResourceInheritsFrom($Resource, $From = null, $Directly = false) {
        TType::Object($Resource, IAclResource::class);
        TType::Object($From, IAclResource::class);
        TType::Bool($Directly);

        $mResourceId = $Resource->getResourceId();
        $mFromId     = '';
        if ($From != null) {
            $mFromId = $From->getResourceId();
            if (!$this->FStorage->HasResource($mFromId)) {
                throw new EAclNoSuchParentResource();
            }
        }

        return $this->FStorage->ResourceInheritsFrom($mResourceId, $mFromId, $Directly);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IAclRole $Role
     * @param \FrameworkDSW\Acl\IAclRole $From
     * @param boolean $Directly
     * @throws EAclNoSuchParentRole
     * @return boolean
     */
    public function RoleInheritsFrom($Role, $From = null, $Directly = false) {
        TType::Object($Role, IAclRole::class);
        TType::Object($From, IAclRole::class);
        TType::Bool($Directly);

        $mRoleId = $Role->getRoleId();
        $mFromId = '';
        if ($From != null) {
            $mFromId = $From->getRoleId();
            if (!$this->FStorage->HasRole($mFromId)) {
                throw new EAclNoSuchParentRole();
            }
        }

        return $this->FStorage->ResourceInheritsFrom($mRoleId, $mFromId, $Directly);
    }

}