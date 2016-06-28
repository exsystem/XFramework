<?php
/**
 * \FrameworkDSW\Acl
 * @author  许子健
 * @version $Id$
 * @since   separate file since reversion 1
 */
namespace FrameworkDSW\Acl;

use FrameworkDSW\Containers\ENoSuchKey;
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
 * \FrameworkDSW\Acl\EMembershipExisted
 *
 * @author 许子健
 */
class EMembershipExisted extends EAclException {
}

/**
 * \FrameworkDSW\Acl\ENoSuchMembership
 *
 * @author 许子健
 */
class ENoSuchMembership extends EAclException {
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
 * params <K: ?, R: ?, M: ?>
 *
 * @author 许子健
 */
interface IAssertion extends IInterface {

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\TAcl $Acl <K: K, R: R, M: M>
     * @param \FrameworkDSW\Acl\IRole $Role
     * @param \FrameworkDSW\Acl\IResource $Resource
     * @param string $Privilege
     * @return boolean
     */
    public static function Assert($Acl, $Role, $Resource, $Privilege);
}

/**
 * \FrameworkDSW\Acl\IStorage
 * params <K: ?, R: ?, M: ?>
 *
 * @author 许子健
 */
interface IStorage extends IInterface {

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IResource $Resource
     * @param string $Parent
     */
    public function AddResource($Resource, $Parent = '');

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IRole $Role
     * @param string $Parent
     */
    public function AddRole($Role, $Parent = '');

    /**
     * descHere
     *
     * @param K $Role
     * @param R $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion<K: K, R: R, M: M>>
     * @param mixed $Option
     */
    public function Allow($Role, $Resource, $Privilege = '', $Assertion = null, &$Option = null);

    /**
     * descHere
     *
     * @param K $Role
     * @param R $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion<K: K, R: R, M: M>>
     */
    public function Deny($Role, $Resource, $Privilege = '', $Assertion = null);

    /**
     * @param string $ResourceId
     * @return R
     */
    public function GetResourceKey($ResourceId);

    /**
     * @param string $RoleId
     * @return K
     */
    public function GetRoleKey($RoleId);

    /**
     * @param string $ResourceId
     * @return \FrameworkDSW\Acl\IResource
     */
    public function GetResource($ResourceId);

    /**
     * @param string $RoleId
     * @return \FrameworkDSW\Acl\IRole
     */
    public function GetRole($RoleId);

    /**
     * descHere
     *
     * @param R $Resource
     * @param mixed $Options
     * @return \FrameworkDSW\Containers\IMap <K: R, V: R[]>
     */
    public function GetResources($Resource, &$Options = null);

    /**
     * descHere
     *
     * @param K $Role
     * @param mixed $Options
     * @return \FrameworkDSW\Containers\IMap <K: K, V: K[]>
     */
    public function GetRoles($Role, &$Options = null);

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
     * @param K $Role
     * @param R $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion<K: K, R: R, M: M>>
     * @return boolean
     */
    public function IsRoleAllowed($Role, $Resource, $Privilege = '', $Assertion = null);

    /**
     * descHere
     *
     * @param R $Resource
     */
    public function RemoveResource($Resource);

    /**
     * descHere
     *
     * @param K $Role
     */
    public function RemoveRole($Role);

    /**
     * @param R $ResourceKey
     * @param \FrameworkDSW\Acl\IResource $Resource
     */
    public function UpdateResource($ResourceKey, $Resource);

    /**
     * @param K $RoleKey
     * @param \FrameworkDSW\Acl\IRole $Role
     */
    public function UpdateRole($RoleKey, $Role);

    /**
     * descHere
     *
     * @param R $Resource
     * @param R $From
     * @param boolean $Directly
     * @return boolean
     */
    public function ResourceInheritsFrom($Resource, $From, $Directly = false);

    /**
     * descHere
     *
     * @param K $Role
     * @param K $From
     * @param boolean $Directly
     * @return boolean
     */
    public function RoleInheritsFrom($Role, $From, $Directly = false);

    /**
     * @return R
     */
    public function getEmptyResourceKey();

    /**
     * @return K
     */
    public function getEmptyRoleKey();

    /**
     * @return M
     */
    public function getEmptyMemberKey();

    /**
     * @param M $Member
     * @param K $Role
     */
    public function AddMembership($Member, $Role);

    /**
     * @param M $Member
     * @param K $Role
     */
    public function RemoveMembership($Member, $Role);

    /**
     * @param M $Member
     * @param K $Role
     * @return boolean
     */
    public function IsMembership($Member, $Role);

    /**
     * @param K $Role
     * @param mixed $Options
     * @return M[]
     */
    public function GetMembers($Role, &$Options = null);

    /**
     * descHere
     *
     * @param M $Member
     * @param R $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion<K: K, R: R, M: M>>
     * @return boolean
     */
    public function IsAllowed($Member, $Resource, $Privilege = '', $Assertion = null);
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
 * extends \FrameworkDSW\Acl\IStorage<K: string, R: string, M: string>
 *
 * @author 许子健
 */
class TRuntimeStorage extends TObject implements IStorage {

    /**
     *
     * @var \FrameworkDSW\Containers\TMap <K: string, V: \FrameworkDSW\Containers\TPair<K: \FrameworkDSW\Acl\IResource, V: string[]>>
     */
    private $FResources = null;
    /**
     *
     * @var \FrameworkDSW\Containers\TMap <K: string, V: \FrameworkDSW\Containers\TPair<K: \FrameworkDSW\Acl\IRole, V: string[]>>
     */
    private $FRoles = null;
    /**
     *
     * @var \FrameworkDSW\Containers\TMap <K: \FrameworkDSW\Containers\TPair<K: string, V: string>, V: \FrameworkDSW\Containers\TList<T: \FrameworkDSW\Containers\TPair<K: string, V: \FrameworkDSW\Reflection\TClass<T: \FrameworkDSW\Acl\IAssertion<K: string, R: string, M: string>>>>>
     */
    private $FRules = null;

    /**
     * @var \FrameworkDSW\Containers\TMap <K: string, V: string[]>
     */
    private $FMembership = null;

    /**
     *
     * @param string $Role
     * @param string $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion<K: string, R: string, M: string>>
     */
    private function DoAllow($Role, $Resource, $Privilege = '', $Assertion = null) {
        TPair::PrepareGeneric(['K' => Framework::String, 'V' => Framework::String]);
        $mRoleResourcePair        = new TPair();
        $mRoleResourcePair->Key   = $Resource;
        $mRoleResourcePair->Value = $Role;

        TPair::PrepareGeneric(['K' => Framework::String, 'V' => [TClass::class => ['T' => IAssertion::class]]]);
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
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion<K: string, R: string, M: string>>
     */
    private function DoDeny($Role, $Resource, $Privilege = '', $Assertion = null) {
        TPair::PrepareGeneric(['K' => Framework::String, 'V' => Framework::String]);
        $mRoleResourcePair        = new TPair();
        $mRoleResourcePair->Key   = $Resource;
        $mRoleResourcePair->Value = $Role;
        if ($this->FRules->ContainsKey($mRoleResourcePair)) {
            TPair::PrepareGeneric(['K' => Framework::String, 'V' => [TClass::class => ['T' => IAssertion::class]]]);
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
        $this->PrepareMethodGeneric(['K' => Framework::String, 'R' => Framework::String, 'M' => Framework::String]);
        parent::__construct();

        TMap::PrepareGeneric(['K' => Framework::String, 'V' => [TPair::class => ['K' => IResource::class, 'V' => Framework::String . '[]']]]);
        $this->FResources = new TMap();
        TMap::PrepareGeneric(['K' => Framework::String, 'V' => [TPair::class => ['K' => IRole::class, 'V' => Framework::String . '[]']]]);
        $this->FRoles = new TMap();
        TMap::PrepareGeneric([
            'K' => [TPair::class => ['K' => Framework::String, 'V' => Framework::String]],
            'V' => [
                TList::class => [
                    'T' => [
                        TPair::class => ['K' => Framework::String, 'V' => [TClass::class => ['T' => IAssertion::class]]]]]]]);
        $this->FRules = new TMap();
        TMap::PrepareGeneric(['K' => Framework::String, 'V' => Framework::String . '[]']);
        $this->FMembership = new TMap();
    }

    /**
     * descHere
     */
    public function Destroy() {
        foreach ($this->FResources as $mKey => $mValue) {
            Framework::Free($mValue->Key);
        }
        foreach ($this->FRoles as $mKey => $mValue) {
            Framework::Free($mValue->Key);
        }
        Framework::Free($this->FResources);
        Framework::Free($this->FRoles);
        Framework::Free($this->FRules);
        Framework::Free($this->FMembership);

        parent::Destroy();
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IResource $Resource
     * @param string $Parent
     */
    public function AddResource($Resource, $Parent = '') {
        TType::Object($Resource, IResource::class);
        TType::String($Parent);

        $mParentPath = [];
        if ($Parent != '') {
            $mParentPath   = $this->FResources[$Parent]->Value;
            $mParentPath[] = $Parent;
        }
        TPair::PrepareGeneric(['K' => IResource::class, 'V' => Framework::String . '[]']);
        $mPair        = new TPair();
        $mPair->Key   = $Resource;
        $mPair->Value = $mParentPath;
        $this->FResources->Put($Resource->getResourceId(), $mPair);
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IRole $Role
     * @param string $Parent
     */
    public function AddRole($Role, $Parent = '') {
        TType::Object($Role, IRole::class);
        TType::String($Parent);

        $mParentPath = [];
        if ($Parent != '') {
            $mParentPath   = $this->FResources[$Parent]->Value;
            $mParentPath[] = $Parent;
        }
        TPair::PrepareGeneric(['K' => IRole::class, 'V' => Framework::String . '[]']);
        $mPair        = new TPair();
        $mPair->Key   = $Role;
        $mPair->Value = $mParentPath;
        $this->FRoles->Put($Role->getRoleId(), $mPair);
    }

    /**
     * descHere
     *
     * @param K $Role
     * @param R $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion<K: string, R: string, M: string>>
     * @param mixed $Option
     */
    public function Allow($Role, $Resource, $Privilege = '', $Assertion = null, &$Option = null) {
        TType::Type($Role, $this->GenericArg('K'));
        TType::Type($Resource, $this->GenericArg('R'));
        TType::String($Privilege);
        TType::Object($Assertion, [TClass::class => ['T' => [IAssertion::class => ['K' => Framework::String, 'R' => Framework::String, 'M' => Framework::String]]]]);

        if ($Role != '' && $Resource != '') {
            $this->DoAllow($Role, $Resource, $Privilege, $Assertion);
        }
        elseif ($Role == '' && $Resource != '') {
            foreach ($this->FRoles as $mRole => $mPair) {
                $this->DoAllow($mRole, $Resource, $Privilege, $Assertion);
            }
        }
        elseif ($Role != '' && $Resource == '') {
            foreach ($this->FResources as $mResource => $mPair) {
                $this->DoAllow($Role, $mResource, $Privilege, $Assertion);
            }
        }
        elseif ($Role == '' && $Resource == '') {
            foreach ($this->FRoles as $mRole => $mPairOuter) {
                /** @noinspection PhpUnusedLocalVariableInspection */
                foreach ($this->FResources as $mResource => $mPairInner) {
                    $this->DoAllow($mRole, $mResource, $Privilege, $Assertion);
                }
            }
        }
    }

    /**
     * descHere
     *
     * @param K $Role
     * @param R $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion<K: string, R: string, M: string>>
     */
    public function Deny($Role, $Resource, $Privilege = '', $Assertion = null) {
        TType::Type($Role, $this->GenericArg('K'));
        TType::Type($Resource, $this->GenericArg('R'));
        TType::String($Privilege);
        TType::Object($Assertion, [TClass::class => ['T' => [IAssertion::class => ['K' => Framework::String, 'R' => Framework::String, 'M' => Framework::String]]]]);

        if ($Role != '' && $Resource != '') {
            $this->DoDeny($Role, $Resource, $Privilege, $Assertion);
        }
        elseif ($Role == '' && $Resource != '') {
            /** @noinspection PhpUnusedLocalVariableInspection */
            foreach ($this->FRoles as $mRole => $mPair) {
                $this->DoDeny($mRole, $Resource, $Privilege, $Assertion);
            }
        }
        elseif ($Role != '' && $Resource == '') {
            /** @noinspection PhpUnusedLocalVariableInspection */
            foreach ($this->FResources as $mResource => $mPair) {
                $this->DoDeny($Role, $mResource, $Privilege, $Assertion);
            }
        }
        elseif ($Role == '' && $Resource == '') {
            foreach ($this->FRoles as $mRole => $mPairOuter) {
                /** @noinspection PhpUnusedLocalVariableInspection */
                foreach ($this->FResources as $mResource => $mPairInner) {
                    $this->DoDeny($mRole, $mResource, $Privilege, $Assertion);
                }
            }
        }
    }

    /**
     * descHere
     *
     * @param R $Resource
     * @param mixed $Options
     * @return \FrameworkDSW\Containers\IMap <K: string, V: string[]>
     */
    public function GetResources($Resource, &$Options = null) {
        TType::Type($Resource, $this->GenericArg('R'));

        if ($Resource == '') {
            return $this->FResources;
        }
        else {
            TMap::PrepareGeneric(['K' => Framework::String, 'V' => Framework::String . '[]']);
            $mResult = new TMap();
            /** @var TPair $mPair */
            foreach ($this->FResources as $mResource => $mPair) {
                if (in_array($Resource, $mPair->Value, true)) {
                    $mResult->Put($mResource, $mPair->Value);
                }
            }

            return $mResult;
        }
    }

    /**
     * descHere
     *
     * @param K $Role
     * @param mixed $Options
     * @return \FrameworkDSW\Containers\IMap <K: string, V: string[]>
     */
    public function GetRoles($Role, &$Options = null) {
        TType::Type($Role, $this->GenericArg('K'));

        TMap::PrepareGeneric(['K' => Framework::String, 'V' => Framework::String . '[]']);
        $mResult = new TMap();
        if ($Role == '') {
            // \FrameworkDSW\Containers\TMap <K: string, V: \FrameworkDSW\Containers\TPair<K: \FrameworkDSW\Acl\IRole, V: string[]>>
            //return $this->FRoles; --  type error. need to be corrected.
            return null;
        }
        else {
            /** @var TPair $mPair */
            foreach ($this->FRoles as $mRole => $mPair) {
                if (in_array($Role, $mPair->Value, true)) {
                    $mResult->Put($mRole, $mPair->Value);
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
     * @param K $Role
     * @param R $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion<K: string, R: string, M: string>>
     * @return boolean
     */
    public function IsRoleAllowed($Role, $Resource, $Privilege = '', $Assertion = null) {
        TType::Type($Role, $this->GenericArg('K'));
        TType::Type($Resource, $this->GenericArg('R'));
        TType::String($Privilege);
        TType::Object($Assertion, [TClass::class => ['T' => [IAssertion::class => ['K' => Framework::String, 'R' => Framework::String, 'M' => Framework::String]]]]);

        TPair::PrepareGeneric(['K' => Framework::String, 'V' => Framework::String]);
        $mResourceRolePair        = new TPair();
        $mResourceRolePair->Key   = $Resource;
        $mResourceRolePair->Value = $Role;

        TPair::PrepareGeneric(['K' => Framework::String, 'V' => [TClass::class => ['T' => [IAssertion::class => ['K' => Framework::String, 'R' => Framework::String, 'M' => Framework::String]]]]]);
        $mPrivilegePair        = new TPair();
        $mPrivilegePair->Key   = $Privilege;
        $mPrivilegePair->Value = $Assertion;

        if (!$this->FRules->ContainsKey($mResourceRolePair)) {
            foreach ($this->FResources[$Resource]->Value as $mAncestorResource) {
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
     * @param R $Resource
     */
    public function RemoveResource($Resource) {
        TType::Type($Resource, $this->GenericArg('R'));

        if ($Resource == '') {
            /** @var TPair $mValue */
            foreach ($this->FResources as $mKey => $mValue) {
                Framework::Free($mValue->Key);
            }
            $this->FResources->Clear();
            $this->FRules->Clear();
        }
        else {
            TLinkedList::PrepareGeneric(['T' => Framework::String]);
            $mPendingResources = new TLinkedList();

            /** @var TPair $mPair */
            foreach ($this->FResources as $mResource => $mPair) {
                if (in_array($Resource, $mPair->Value, true)) {
                    Framework::Free($mPair->Key);
                    $this->FResources->Delete($mResource);
                    $mPendingResources->Add($Resource);
                }
            }

            Framework::Free($this->FResources[$Resource]->Key);
            $this->FResources->Delete($Resource);

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
     * @param K $Role
     */
    public function RemoveRole($Role) {
        TType::Type($Role, $this->GenericArg('K'));

        if ($Role == '') {
            /** @var TPair $mValue */
            foreach ($this->FRoles as $mKey => $mValue) {
                Framework::Free($mValue->Key);
            }
            $this->FRoles->Clear();
            $this->FRules->Clear();
        }
        else {
            TLinkedList::PrepareGeneric(['T' => Framework::String]);
            $mPendingRoles = new TLinkedList();

            /** @var TPair $mPair */
            foreach ($this->FRoles as $mRole => $mPair) {
                if (in_array($Role, $mPair->Value, true)) {
                    Framework::Free($mPair->Key);
                    $this->FResources->Delete($mRole);
                    $mPendingRoles->Add($mRole);
                }
            }

            Framework::Free($this->FRoles[$Role]->Key);
            $this->FRoles->Delete($Role);

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
     * @param R $Resource
     * @param R $From
     * @param boolean $Directly
     * @return boolean
     */
    public function ResourceInheritsFrom($Resource, $From, $Directly = false) {
        TType::Type($Resource, $this->GenericArg('R'));
        TType::Type($From, $this->GenericArg('R'));
        TType::Bool($Directly);

        $mPath = $this->FResources[$Resource]->Value;
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
     * @param K $Role
     * @param K $From
     * @param boolean $Directly
     * @return boolean
     */
    public function RoleInheritsFrom($Role, $From, $Directly = false) {
        TType::Type($Role, $this->GenericArg('K'));
        TType::Type($From, $this->GenericArg('K'));
        TType::Bool($Directly);

        $mPath = $this->FRoles[$Role]->Value;
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
     * @param string $ResourceId
     * @return \FrameworkDSW\Acl\IResource
     */
    public function GetResource($ResourceId) {
        TType::String($ResourceId);

        /** @var TPair $mPair */
        $mPair = $this->FResources[$ResourceId];
        if ($mPair->Key === null) {
            $mPair->Key = new TResource($ResourceId);
        }
        return $mPair->Key;
    }

    /**
     * @param string $RoleId
     * @return \FrameworkDSW\Acl\IRole
     */
    public function GetRole($RoleId) {
        TType::String($RoleId);

        /** @var TPair $mPair */
        $mPair = $this->FRoles[$RoleId];
        if ($mPair->Key === null) {
            $mPair->Key = new TRole($RoleId);
        }
        return $mPair->Key;
    }

    /**
     * @param R $ResourceKey
     * @param \FrameworkDSW\Acl\IResource $Resource
     */
    public function UpdateResource($ResourceKey, $Resource) {
        TType::Type($ResourceKey, $this->GenericArg('R'));
        TType::Object($Resource, IResource::class);

        /** @var TPair $mResourcePathPair */
        $mResourcePathPair = $this->FResources[$ResourceKey];
        if ($mResourcePathPair->Key !== $Resource) {
            Framework::Free($mResourcePathPair->Key);
            $mResourcePathPair->Key = new TResource($Resource->getResourceId());
        }
        if ($ResourceKey != $Resource->getResourceId()) {
            $this->FResources->Delete($ResourceKey);
            $this->FResources->Put($Resource->getResourceId(), $mResourcePathPair);

            TMap::PrepareGeneric(['K' => [TPair::class => ['K' => Framework::String, 'V' => Framework::String]], 'V' => [TList::class => ['T' => [TPair::class => ['K' => Framework::String, 'V' => [TClass::class => ['T' => IAssertion::class]]]]]]]);
            $mRules = new TMap();
            /** @var TPair $mKey */
            foreach ($this->FRules as $mKey => $mValue) {
                if ($mKey->Key == $ResourceKey) {
                    TList::PrepareGeneric(['T' => [TPair::class => ['K' => Framework::String, 'V' => [TClass::class => ['T' => IAssertion::class]]]]]);
                    $mNewValue = new TList();
                    $mNewValue->AddAll($mValue);
                    $mRules->Put($mKey, $mNewValue);
                }
            }
            foreach ($mRules as $mKey => $mValue) {
                $this->FRules->Delete($mKey);
            }
            foreach ($mRules as $mKey => $mValue) {
                $mKey->Key = $Resource->getResourceId();
                $mRules->Put($mKey, $mValue);
            }
            Framework::Free($mRules);
        }
    }

    /**
     * @param K $RoleKey
     * @param \FrameworkDSW\Acl\IRole $Role
     */
    public function UpdateRole($RoleKey, $Role) {
        TType::Type($RoleKey, $this->GenericArg('K'));
        TType::Object($Role, IRole::class);

        /** @var TPair $mRolePathPair */
        $mRolePathPair = $this->FRoles[$RoleKey];
        if ($mRolePathPair->Key !== $Role) {
            Framework::Free($mRolePathPair->Key);
            $mRolePathPair->Key = new TRole($Role->getRoleId());
        }
        if ($RoleKey != $Role->getRoleId()) {
            $this->FRoles->Delete($RoleKey);
            $this->FRoles->Put($Role->getRoleId(), $mRolePathPair);

            TMap::PrepareGeneric(['K' => [TPair::class => ['K' => Framework::String, 'V' => Framework::String]], 'V' => [TList::class => ['T' => [TPair::class => ['K' => Framework::String, 'V' => [TClass::class => ['T' => IAssertion::class]]]]]]]);
            $mRules = new TMap();
            /** @var TPair $mKey */
            foreach ($this->FRules as $mKey => $mValue) {
                if ($mKey->Value == $RoleKey) {
                    TList::PrepareGeneric(['T' => [TPair::class => ['K' => Framework::String, 'V' => [TClass::class => ['T' => IAssertion::class]]]]]);
                    $mNewValue = new TList();
                    $mNewValue->AddAll($mValue);
                    $mRules->Put($mKey, $mNewValue);
                }
            }
            foreach ($mRules as $mKey => $mValue) {
                $this->FRules->Delete($mKey);
            }
            foreach ($mRules as $mKey => $mValue) {
                $mKey->Value = $Role->getRoleId();
                $mRules->Put($mKey, $mValue);
            }
            Framework::Free($mRules);
        }
    }

    /**
     * @param string $ResourceId
     * @return R
     */
    public function GetResourceKey($ResourceId) {
        TType::String($ResourceId);
        return $ResourceId;
    }

    /**
     * @param string $RoleId
     * @return K
     */
    public function GetRoleKey($RoleId) {
        TType::String($RoleId);
        return $RoleId;
    }

    /**
     * @return R
     */
    public function getEmptyResourceKey() {
        return '';
    }

    /**
     * @return K
     */
    public function getEmptyRoleKey() {
        return '';
    }

    /**
     * @return M
     */
    public function getEmptyMemberKey() {
        return '';
    }

    /**
     * @param M $Member
     * @param K $Role
     */
    public function AddMembership($Member, $Role) {
        TType::Type($Member, $this->GenericArg('M'));
        TType::Type($Role, $this->GenericArg('K'));

        if ($this->FMembership->ContainsKey($Member)) {
            $this->FMembership[$Member][] = $Role;
        }
        else {
            $this->FMembership->Put($Member, [$Role]);
        }
    }

    /**
     * @param M $Member
     * @param K $Role
     */
    public function RemoveMembership($Member, $Role) {
        TType::Type($Member, $this->GenericArg('M'));
        TType::Type($Role, $this->GenericArg('K'));

        if ($this->FMembership->ContainsKey($Member)) {
            foreach ($this->FMembership[$Member] as $mKey => $mRole) {
                if ($mRole == $Role) {
                    array_splice($this->FMembership[$Member], $mKey, 1);
                    return;
                }
            }
        }
    }

    /**
     * @param M $Member
     * @param K $Role
     * @return boolean
     */
    public function IsMembership($Member, $Role) {
        TType::Type($Member, $this->GenericArg('M'));
        TType::Type($Role, $this->GenericArg('K'));

        if ($this->FMembership->ContainsKey($Member)) {
            return in_array($Role, $this->FMembership[$Member], true);
        }
        else {
            return false;
        }
    }

    /**
     * @param K $Role
     * @param mixed $Options
     * @return M[]
     */
    public function GetMembers($Role, &$Options = null) {
        TType::Type($Role, $this->GenericArg('K'));

        $mResult = [];
        foreach ($this->FMembership as $mMember => $mRoles) {
            if (in_array($Role, $mRoles, true)) {
                $mResult[] = $mMember;
            }
        }
        return $mResult;
    }

    /**
     * descHere
     *
     * @param M $Member
     * @param R $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion<K: string, R: string, M: string>>
     * @return boolean
     */
    public function IsAllowed($Member, $Resource, $Privilege = '', $Assertion = null) {
        TType::Type($Member, $this->GenericArg('M'));
        TType::Type($Role, $this->GenericArg('R'));
        TType::String($Privilege);
        TType::Object($Assertion, [TClass::class => ['T' => [IAssertion::class => ['K' => Framework::String, 'R' => Framework::String, 'M' => Framework::String]]]]);

        if ($this->FMembership->ContainsKey($Member)) {
            foreach ($this->FMembership[$Member] as $mRole) {
                if ($this->IsRoleAllowed($mRole, $Resource, $Privilege, $Assertion)) {
                    return true;
                }
            }
        }
        return false;
    }
}

/**
 * \FrameworkDSW\Acl\TAcl
 * param <K: ?, R: ?, M: ?>
 * @author 许子健
 */
class TAcl extends TObject {
    /**
     *
     * @var \FrameworkDSW\Acl\IStorage <K: K, R: R, M: M>
     */
    private $FStorage = null;
    /**
     * @var \FrameworkDSW\Containers\TMap <K: string, V: \FrameworkDSW\Acl\IResource>
     */
    private $FResources = null;
    /**
     * @var \FrameworkDSW\Containers\TMap <K: string, V: \FrameworkDSW\Acl\IRole>
     */
    private $FRoles = null;

    /**
     * @param \FrameworkDSW\Acl\IRole $Role
     * @return string
     * @throws \FrameworkDSW\Acl\ENoSuchRole
     */
    private function EnsureRoleExists($Role) {
        $mRoleId = $Role->getRoleId();
        if ($this->FStorage->HasRole($mRoleId)) {
            return $mRoleId;
        }
        else {
            throw new ENoSuchRole(sprintf('No such role: %s.', $mRoleId));
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IStorage $Storage <K: K, R: R, M: M>
     */
    public function __construct($Storage = null) {
        parent::__construct();
        TType::Object($Storage, [IStorage::class => ['K' => $this->GenericArg('K'), 'R' => $this->GenericArg('R'), 'M' => $this->GenericArg('M')]]);

        $this->FStorage = $Storage;
        TMap::PrepareGeneric(['K' => Framework::String, 'V' => IResource::class]);
        $this->FResources = new TMap(true);
        TMap::PrepareGeneric(['K' => Framework::String, 'V' => IRole::class]);
        $this->FRoles = new TMap(true);
    }

    /**
     *
     */
    public function Destroy() {
        Framework::Free($this->FResources);
        Framework::Free($this->FRoles);
        parent::Destroy();
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IResource $Resource
     * @param \FrameworkDSW\Acl\IResource $Parent
     * @return \FrameworkDSW\Acl\TAcl <K: K, R: R, M: M>
     * @throws \FrameworkDSW\Acl\ENoSuchParentResource
     * @throws \FrameworkDSW\Acl\EResourceExisted
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     */
    public function AddResource($Resource, $Parent = null) {
        TType::Object($Resource, IResource::class);
        TType::Object($Parent, IResource::class);

        $mResourceId = $Resource->getResourceId();

        if ($mResourceId == '') {
            throw new EInvalidParameter(sprintf('Invalid resource ID: Empty ID is not allowed.'));
        }

        if ($Parent === null) {
            $mParentId = '';
        }
        else {
            $mParentId = $Parent->getResourceId();
//            if (!$this->FStorage->HasResource($mParentId)) {
//                throw new ENoSuchParentResource(sprintf('No such parent resource: %s.', $Parent->getResourceId()));
//            }
        }
        if ($this->FStorage->HasResource($mResourceId)) {
            throw new EResourceExisted(sprintf('Resource existed: %s.', $mResourceId));
        }

        $this->FStorage->AddResource($Resource, $mParentId);
        $this->FResources->Put($mResourceId, $Resource);
        return $this;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IRole $Role
     * @param \FrameworkDSW\Acl\IRole $Parent
     * @throws ERoleExisted
     * @throws ENoSuchParentRole
     * @return \FrameworkDSW\Acl\TAcl <K: K, R: R, M: M>
     */
    public function AddRole($Role, $Parent = null) {
        TType::Object($Role, IRole::class);
        TType::Object($Parent, IRole::class);

        $mRoleId = $Role->getRoleId();
        if ($Parent === null) {
            $mParentId = '';
        }
        else {
            $mParentId = $Parent->getRoleId();
//            if ($this->FStorage->HasRole($mParentId)) {
//                throw new ENoSuchParentRole(sprintf('No such parent role: %s.', $Parent->getRoleId()));
//            }
        }
        if ($this->FStorage->HasRole($mRoleId)) {
            throw new ERoleExisted(sprintf('Role existed: %s.', $mRoleId));
        }

        $this->FStorage->AddRole($Role, $mParentId);
        $this->FRoles->Put($mRoleId, $Role);
        return $this;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IRole $Role
     * @param \FrameworkDSW\Acl\IResource $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion<K: K, R: R, M: M>>
     * @param mixed $Option
     * @throws ENoSuchRole
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return \FrameworkDSW\Acl\TAcl <K: K, R: R, M: M>
     */
    public function Allow($Role = null, $Resource = null, $Privilege = '', $Assertion = null, &$Option = null) {
        TType::Object($Role, IRole::class);
        TType::Object($Resource, IResource::class);
        TType::String($Privilege);
        TType::Object($Assertion, [TClass::class => ['T' => [IAssertion::class => ['K' => $this->GenericArg('K'), 'R' => $this->GenericArg('R'), 'M' => $this->GenericArg('M')]]]]);

        if ($Privilege == '' && $Assertion === null) {
            throw new EInvalidParameter(sprintf('Invalid parameter: empty $Privilege and null $Assertion appeared at same time.'));
        }
        if ($Privilege != '' && $Assertion !== null) {
            throw new EInvalidParameter(sprintf('Invalid parameter: non-empty $Privilege and non-null $Assertion appeared at same time.'));
        }

        if ($Resource === null) {
            $mResourceKey = $this->FStorage->getEmptyResourceKey();
        }
        else {
            $mResourceKey = $this->FStorage->GetResourceKey($Resource->getResourceId());
        }
        if ($Role === null) {
            $mRoles = $this->FStorage->GetRoles($this->FStorage->getEmptyRoleKey());
            /** @noinspection PhpUnusedLocalVariableInspection */
            foreach ($mRoles as $mRoleKey => &$mPath) {
                $this->FStorage->Allow($mRoleKey, $mResourceKey, $Privilege, $Assertion, $Option);
            }
            Framework::Free($mRoles);
        }
        else {
            $mRoleId = $Role->getRoleId();
            if (!$this->FStorage->HasRole($mRoleId)) {
                throw new ENoSuchRole(sprintf('No such role: %s.', $mRoleId));
            }
            $this->FStorage->Allow($this->FStorage->GetRoleKey($mRoleId), $mResourceKey, $Privilege, $Assertion, $Option);
        }

        return $this;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IRole $Role
     * @param \FrameworkDSW\Acl\IResource $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion<K: K, R: R, M: M>>
     * @throws ENoSuchRole
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return \FrameworkDSW\Acl\TAcl <K: K, R: R, M: M>
     */
    public function Deny($Role = null, $Resource = null, $Privilege = '', $Assertion = null) {
        TType::Object($Role, IRole::class);
        TType::Object($Resource, IResource::class);
        TType::String($Privilege);
        TType::Object($Assertion, [TClass::class => ['T' => [IAssertion::class => ['K' => $this->GenericArg('K'), 'R' => $this->GenericArg('R'), 'M' => $this->GenericArg('M')]]]]);

        if ($Privilege == '' && $Assertion === null) {
            throw new EInvalidParameter(sprintf('Invalid parameter: empty $Privilege and null $Assertion appeared at same time.'));
        }
        if ($Privilege != '' && $Assertion !== null) {
            throw new EInvalidParameter(sprintf('Invalid parameter: non-empty $Privilege and non-null $Assertion appeared at same time.'));
        }

        if ($Resource === null) {
            $mResourceKey = $this->FStorage->getEmptyResourceKey();
        }
        else {
            $mResourceKey = $this->FStorage->GetResourceKey($Resource->getResourceId());
        }
        if ($Role === null) {
            $mRoles = $this->FStorage->GetRoles($this->FStorage->getEmptyRoleKey());
            /** @noinspection PhpUnusedLocalVariableInspection */
            foreach ($mRoles as $mRoleKey => &$mPath) {
                $this->FStorage->Deny($mRoleKey, $mResourceKey, $Privilege, $Assertion);
            }
            Framework::Free($mRoles);
        }
        else {
            $mRoleId = $Role->getRoleId();
            if (!$this->FStorage->HasRole($mRoleId)) {
                throw new ENoSuchRole(sprintf('No such role: %s.', $mRoleId));
            }
            $this->FStorage->Deny($this->FStorage->GetRoleKey($mRoleId), $mResourceKey, $Privilege, $Assertion);
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
    public function GetResourceById($ResourceId) {
        TType::String($ResourceId);

        if ($this->FStorage->HasResource($ResourceId)) {
            return $this->DoGetResource($ResourceId);
        }
        else {
            throw new ENoSuchResource(sprintf('No such resource: %s.', $ResourceId));
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IResource $Resource
     * @param mixed $Options
     * @throws ENoSuchResource
     * @return \FrameworkDSW\Containers\IMap <K: \FrameworkDSW\Acl\IResource, V: \FrameworkDSW\Containers\IList<T: \FrameworkDSW\Acl\IResource>>
     */
    public function GetResources($Resource = null, &$Options = null) {
        TType::Object($Resource, IResource::class);

        if ($Resource === null) {
            $mResourceId = '';
        }
        else {
            $mResourceId = $Resource->getResourceId();
            if (!$this->FStorage->HasResource($mResourceId)) {
                throw new ENoSuchResource(sprintf('No such resource: %s.', $mResourceId));
            }
        }
        $mRaw = $this->FStorage->GetResources($this->FStorage->GetResourceKey($mResourceId), $Options);
        TMap::PrepareGeneric(['K' => IResource::class, 'V' => [IList::class => ['T' => IResource::class]]]);
        $mResult = new TMap(true);
        foreach ($mRaw as $mRawResource => $mRawPath) {
            TList::PrepareGeneric(['T' => TResource::class]);
            $mPath = new TList(count($mRawPath));
            foreach ($mRawPath as $mRawPathElement) {
                $mPath->Add($this->DoGetResource($mRawPathElement));
            }
            $mResult->Put($this->DoGetResource($mRawResource), $mPath);
        }
        Framework::Free($mRaw);

        return $mResult;
    }

    /**
     * descHere
     *
     * @param string $ResourceId
     * @param mixed $Options
     * @return \FrameworkDSW\Containers\IMap <K: \FrameworkDSW\Acl\IResource, V: \FrameworkDSW\Containers\IList<T: \FrameworkDSW\Acl\IResource>>
     * @throws ENoSuchResource
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     */
    public function GetResourcesById($ResourceId = '', &$Options = null) {
        TType::String($ResourceId);

        if ($ResourceId == '') {
            return $this->GetResources(null, $Options);
        }
        else {
            return $this->GetResources($this->GetResourceById($ResourceId), $Options);
        }
    }

    /**
     * descHere
     *
     * @param string $RoleId
     * @throws ENoSuchRole
     * @return \FrameworkDSW\Acl\IRole
     */
    public function GetRoleById($RoleId) {
        TType::String($RoleId);

        if ($this->FStorage->HasRole($RoleId)) {
            return $this->DoGetRole($RoleId);
        }
        else {
            throw new ENoSuchRole(sprintf('No such role: %s.', $RoleId));
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IRole $Role
     * @param mixed $Options
     * @throws ENoSuchRole
     * @return \FrameworkDSW\Containers\IMap <K: \FrameworkDSW\Acl\IRole, V: IList<T: \FrameworkDSW\Acl\IRole>>
     */
    public function GetRoles($Role = null, &$Options = null) {
        TType::Object($Role, IResource::class);

        if ($Role === null) {
            $mRoleId = '';
        }
        else {
            $mRoleId = $this->EnsureRoleExists($Role);
        }
        $mRaw = $this->FStorage->GetRoles($this->FStorage->GetRoleKey($mRoleId), $Options);
        TMap::PrepareGeneric(['K' => IRole::class, 'V' => [IList::class => ['T' => IRole::class]]]);
        $mResult = new TMap(true);
        foreach ($mRaw as $mRawRole => $mRawPath) {
            TList::PrepareGeneric(['T' => TResource::class]);
            $mPath = new TList(count($mRawPath));
            foreach ($mRawPath as $mRawPathElement) {
                $mPath->Add($this->DoGetRole($mRawPathElement));
            }
            $mResult->Put($this->DoGetRole($mRawRole), $mPath);
        }
        Framework::Free($mRaw);

        return $mResult;
    }

    /**
     * descHere
     *
     * @param string $RoleId
     * @param mixed $Options
     * @return \FrameworkDSW\Containers\IMap <K: \FrameworkDSW\Acl\IRole, V: \FrameworkDSW\Containers\IList<T: \FrameworkDSW\Acl\IRole>>
     */
    public function GetRolesById($RoleId = '', &$Options = null) {
        TType::String($RoleId);

        if ($RoleId == '') {
            return $this->GetRoles(null, $Options);
        }
        else {
            return $this->GetRoles($this->GetRoleById($RoleId), $Options);
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
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion<K: K, R: R, M: M>>
     * @throws ENoSuchResource
     * @throws ENoSuchRole
     * @throws \FrameworkDSW\System\EInvalidParameter
     * @return boolean
     */
    public function IsRoleAllowed($Role = null, $Resource = null, $Privilege = '', $Assertion = null) {
        TType::Object($Role, IRole::class);
        TType::Object($Resource, IResource::class);
        TType::String($Privilege);
        TType::Object($Assertion, [TClass::class => ['T' => [IAssertion::class => ['K' => $this->GenericArg('K'), 'R' => $this->GenericArg('R'), 'M' => $this->GenericArg('M')]]]]);

        if ($Privilege == '' && $Assertion === null) {
            throw new EInvalidParameter(sprintf('Invalid parameter: empty $Privilege and null $Assertion appeared at same time.'));
        }
        if ($Privilege != '' && $Assertion !== null) {
            throw new EInvalidParameter(sprintf('Invalid parameter: non-empty $Privilege and non-null $Assertion appeared at same time.'));
        }

        if ($Resource === null) {
            $mResourceKey = $this->FStorage->getEmptyResourceKey();
        }
        else {
            $mResourceId = $Resource->getResourceId();
            if (!$this->FStorage->HasResource($mResourceId)) {
                throw new ENoSuchResource(sprintf('No such resource: %s.', $mResourceId));
            }
            $mResourceKey = $this->FStorage->GetResourceKey($mResourceId);
        }
        if ($Role === null) {
            $mRoles = $this->FStorage->GetRoles($this->FStorage->getEmptyRoleKey());
            foreach ($mRoles as $mRoleKey) {
                if (!$this->FStorage->IsRoleAllowed($mRoleKey, $mResourceKey, $Privilege, $Assertion)) {
                    Framework::Free($mRoles);

                    return false;
                }
            }
            Framework::Free($mRoles);

            return true;
        }
        else {
            return $this->FStorage->IsRoleAllowed($this->FStorage->GetRoleKey($Role->getRoleId()), $mResourceKey, $Privilege, $Assertion);
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IResource $Resource
     * @throws ENoSuchResource
     * @return \FrameworkDSW\Acl\TAcl <K: K, R: R, M: M>
     */
    public function RemoveResource($Resource = null) {
        TType::Object($Resource, IResource::class);

        if ($Resource === null) {
            $this->FStorage->RemoveResource($this->FStorage->getEmptyResourceKey());
            $this->FResources->Clear();
        }
        else {
            $mResourceId = $Resource->getResourceId();
            if ($this->FStorage->HasResource($mResourceId)) {
                $this->FStorage->RemoveResource($this->FStorage->GetResourceKey($mResourceId));
                if ($this->FResources->ContainsKey($mResourceId)) {
                    $this->FResources->Delete($mResourceId);
                }
            }
            else {
                throw new ENoSuchResource(sprintf('No such resource: %s.', $mResourceId));
            }
        }

        return $this;
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Acl\IRole $Role
     * @throws ENoSuchRole
     * @return \FrameworkDSW\Acl\TAcl <K: K, R: R, M: M>
     */
    public function RemoveRole($Role = null) {
        TType::Object($Role, IRole::class);

        if ($Role === null) {
            $this->FStorage->RemoveRole($this->FStorage->getEmptyRoleKey());
        }
        else {
            $mRoleId = $this->EnsureRoleExists($Role);
            $this->FStorage->RemoveRole($this->FStorage->GetRoleKey($mRoleId));
            if ($this->FRoles->ContainsKey($mRoleId)) {
                $this->FRoles->Delete($mRoleId);
            }
        }

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
        if ($From !== null) {
            $mFromId = $From->getResourceId();
            if (!$this->FStorage->HasResource($mFromId)) {
                throw new ENoSuchParentResource(sprintf('No such parent resource: %s.', $mFromId));
            }
        }

        return $this->FStorage->ResourceInheritsFrom($this->FStorage->GetResourceKey($mResourceId), $this->FStorage->GetResourceKey($mFromId), $Directly);
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

        $mRoleId = $this->EnsureRoleExists($Role);
        $mFromId = '';
        if ($From !== null) {
            $mFromId = $From->getRoleId();
            if (!$this->FStorage->HasRole($mFromId)) {
                throw new ENoSuchParentRole(sprintf('No such parent role: %s.', $mFromId));
            }
        }

        return $this->FStorage->RoleInheritsFrom($this->FStorage->GetRoleKey($mRoleId), $this->FStorage->GetRoleKey($mFromId), $Directly);
    }

    /**
     * @param string $ResourceId
     * @return \FrameworkDSW\Acl\IResource
     */
    private function DoGetResource($ResourceId) {
        if ($this->FResources->ContainsKey($ResourceId)) {
            $mResult = $this->FResources[$ResourceId];
            if ($mResult === null) {
                $mResult                       = $this->FStorage->GetResource($ResourceId);
                $this->FResources[$ResourceId] = $mResult;
            }
        }
        else {
            $mResult = $this->FStorage->GetResource($ResourceId);
            $this->FResources->Put($ResourceId, $mResult);
        }
        return $mResult;
    }

    /**
     * @param string $ResourceId
     * @param \FrameworkDSW\Acl\IResource $Resource
     * @throws \FrameworkDSW\Acl\ENoSuchResource
     * @throws \FrameworkDSW\Acl\EResourceExisted
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     * @return \FrameworkDSW\Acl\TAcl <K: K, R: R, M: M>
     */
    public function UpdateResource($ResourceId, $Resource) {
        TType::String($ResourceId);
        TType::Object($Resource, IResource::class);

        if (!$this->FStorage->HasResource($ResourceId)) {
            throw new ENoSuchResource(sprintf('No such resource: %s.', $ResourceId));
        }
        if ($ResourceId != $Resource->getResourceId() && $this->FStorage->HasResource($Resource->getResourceId())) {
            throw new EResourceExisted(sprintf('Resource existed: %s.', $ResourceId));
        }

        $this->FStorage->UpdateResource($this->FStorage->GetResourceKey($ResourceId), $Resource);
        try {
            if ($this->FResources[$ResourceId] !== $Resource) {
                $this->FResources->Delete($ResourceId);
                Framework::Free($Resource);
            }
        }
        catch (ENoSuchKey $Ex) {
            //do nothing.
        }
        finally {
            return $this;
        }
    }

    /**
     * @param string $RoleId
     * @param \FrameworkDSW\Acl\IRole $Role
     * @throws \FrameworkDSW\Acl\ENoSuchRole
     * @throws \FrameworkDSW\Acl\ERoleExisted
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     * @return \FrameworkDSW\Acl\TAcl <K: K, R: R, M: M>
     */
    public function UpdateRole($RoleId, $Role) {
        TType::String($RoleId);
        TType::Object($Role, IRole::class);

        if (!$this->FStorage->HasRole($RoleId)) {
            throw new ENoSuchRole(sprintf('No such role: %s.', $RoleId));
        }
        if ($RoleId != $Role->getRoleId() && $this->FStorage->HasRole($Role->getRoleId())) {
            throw new ERoleExisted(sprintf('Role existed: %s.', $RoleId));
        }
        $this->FStorage->UpdateRole($this->FStorage->GetRoleKey($RoleId), $Role);
        try {
            if ($this->FRoles[$RoleId] !== $Role) {
                $this->FRoles->Delete($RoleId);
                Framework::Free($Role);
            }
        }
        catch (ENoSuchKey $Ex) {
            //do nothing.
        }
        finally {
            return $this;
        }
    }

    /**
     * @param string $RoleId
     * @return \FrameworkDSW\Acl\IRole
     */
    private function DoGetRole($RoleId) {
        if ($this->FRoles->ContainsKey($RoleId)) {
            $mResult = $this->FRoles[$RoleId];
            if ($mResult === null) {
                $mResult               = $this->FStorage->GetRole($RoleId);
                $this->FRoles[$RoleId] = $mResult;
            }
        }
        else {
            $mResult = $this->FStorage->GetRole($RoleId);
            $this->FRoles->Put($RoleId, $mResult);
        }
        return $mResult;
    }

    /**
     * @param M $Member
     * @param \FrameworkDSW\Acl\IRole $Role
     * @throws \FrameworkDSW\Acl\EMembershipExisted
     * @throws \FrameworkDSW\Acl\ENoSuchRole
     * @throws \FrameworkDSW\System\ENoSuchGenericArg
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     * @return \FrameworkDSW\Acl\TAcl <K: K, R: R, M: M>
     */
    public function AddMembership($Member, $Role) {
        TType::Type($Member, $this->GenericArg('M'));
        TType::Object($Role, IRole::class);

        $mRoleKey = $this->FStorage->GetRoleKey($this->EnsureRoleExists($Role));
        if ($this->FStorage->IsMembership($Member, $mRoleKey)) {
            throw new EMembershipExisted(sprintf('Membership existed.'));
        }
        $this->FStorage->AddMembership($Member, $mRoleKey);
        return $this;
    }

    /**
     * @param M $Member
     * @param \FrameworkDSW\Acl\IRole $Role
     * @throws \FrameworkDSW\Acl\ENoSuchMembership
     * @throws \FrameworkDSW\Acl\ENoSuchRole
     * @throws \FrameworkDSW\System\ENoSuchGenericArg
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     * @return \FrameworkDSW\Acl\TAcl <K: K, R: R, M: M>
     */
    public function RemoveMembership($Member, $Role) {
        TType::Type($Member, $this->GenericArg('M'));
        TType::Object($Role, IRole::class);

        $mRoleKey = $this->FStorage->GetRoleKey($this->EnsureRoleExists($Role));
        if (!$this->FStorage->IsMembership($Member, $mRoleKey)) {
            throw new ENoSuchMembership(sprintf('No such membership.'));
        }
        $this->FStorage->RemoveMembership($Member, $mRoleKey);
        return $this;
    }

    /**
     * @param M $Member
     * @param \FrameworkDSW\Acl\IRole $Role
     * @return boolean
     * @throws \FrameworkDSW\Acl\ENoSuchRole
     * @throws \FrameworkDSW\System\ENoSuchGenericArg
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     */
    public function IsMembership($Member, $Role) {
        TType::Type($Member, $this->GenericArg('M'));
        TType::Object($Role, IRole::class);

        return $this->FStorage->IsMembership($Member, $this->FStorage->GetRoleKey($this->EnsureRoleExists($Role)));
    }

    /**
     * @param \FrameworkDSW\Acl\IRole $Role
     * @param mixed $Options
     * @return M[]
     */
    public function GetMembers($Role, &$Options = null) {
        TType::Object($Role, IRole::class);

        return $this->FStorage->GetMembers($this->FStorage->GetRoleKey($this->EnsureRoleExists($Role)), $Options);
    }

    /**
     * descHere
     *
     * @param M $Member
     * @param \FrameworkDSW\Acl\IResource $Resource
     * @param string $Privilege
     * @param \FrameworkDSW\Reflection\TClass $Assertion <T: \FrameworkDSW\Acl\IAssertion<K: K, R: R, M: M>>
     * @return boolean
     * @throws \FrameworkDSW\Acl\ENoSuchResource
     * @throws \FrameworkDSW\System\ENoSuchGenericArg
     * @throws \FrameworkDSW\Utilities\EInvalidObjectCasting
     * @throws \FrameworkDSW\Utilities\EInvalidStringCasting
     */
    public function IsAllowed($Member, $Resource, $Privilege = '', $Assertion = null) {
        TType::Type($Member, $this->GenericArg('M'));
        TType::Type($Resource, IResource::class);
        TType::String($Privilege);
        TType::Object($Assertion, [TClass::class => ['T' => [IAssertion::class => ['K' => $this->GenericArg('K'), 'R' => $this->GenericArg('R'), 'M' => $this->GenericArg('M')]]]]);

        $mResourceId = $Resource->getResourceId();
        if ($this->FStorage->HasResource($mResourceId)) {
            return $this->FStorage->IsAllowed($Member, $this->FStorage->GetResourceKey($mResourceId), $Privilege, $Assertion);
        }
        else {
            throw new ENoSuchResource(sprintf('No such resource: %s.', $mResourceId));
        }
    }
}