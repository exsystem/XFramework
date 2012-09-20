<?php
/**
 * Acl
 * @author  许子健
 * @version $Id$
 * @since   separate file since reversion 1
 */

require_once 'FrameworkDSW/Containers.php';

/**
 * EAclException
 *
 * @author 许子健
 */
class EAclException extends EException {}
/**
 * EAclResourceExisted
 *
 * @author 许子健
 */
class EAclResourceExisted extends EAclException {}
/**
 * EAclNoSuchParentResource
 *
 * @author 许子健
 */
class EAclNoSuchParentResource extends EAclException {}
/**
 * EAclRoleExisted
 *
 * @author 许子健
 */
class EAclRoleExisted extends EAclException {}
/**
 * EAclNoSuchParentRole
 *
 * @author 许子健
 */
class EAclNoSuchParentRole extends EAclException {}
/**
 * EAclNoSuchRole
 *
 * @author 许子健
 */
class EAclNoSuchRole extends EAclException {}
/**
 * EAclNoSuchResource
 *
 * @author 许子健
 */
class EAclNoSuchResource extends EAclException {}

/**
 * IAclRole
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
 * IAclResource
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
 * IAclAssertion
 *
 * @author 许子健
 */
interface IAclAssertion extends IInterface {

    /**
     * descHere
     *
     * @param TAcl $ACL            
     * @param IAclRole $Role            
     * @param IAclResource $Resource            
     * @param string $Privilege            
     * @return boolean
     */
    public static function Assert($ACL, $Role, $Resource, $Privilege);
}

/**
 * IAclStorage
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
     * @return IMap <K: string, V: string[]>
     */
    public function getResources($Resource = '');

    /**
     * descHere
     *
     * @param string $Role            
     * @return IMap <K: string, V: string[]>
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
 * TAclRole
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
 * TAclResource
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
 * TRuntimeAclStorage
 *
 * @author 许子健
 */
class TRuntimeAclStorage extends TObject implements IAclStorage {
    
    /**
     *
     * @var TMap <K: string, V: string[]>
     */
    private $FResources = null;
    /**
     *
     * @var TMap <K: string, V: string[]>
     */
    private $FRoles = null;
    /**
     *
     * @var TMap <K: TPair<K: string, V: string>, V: TList<T: TPair<K: string,
     *      V: mixed>>>
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
        $mRoleResourcePair = new TPair();
        $mRoleResourcePair->Key = $Resource;
        $mRoleResourcePair->Value = $Role;
        
        $mPrivilegePair = new TPair();
        $mPrivilegePair->Key = $Privilege;
        $mPrivilegePair->Value = $Assertion;
        
        if ($this->FRules->ContainsKey($mRoleResourcePair)) {
            if (!$this->FRules[$mRoleResourcePair]->Contains($mPrivilegePair)) {
                $this->FRules[$mRoleResourcePair]->Add($mPrivilegePair);
            }
        }
        else {
            TList::PrepareGeneric(array (
                'T' => array (
                    'TPair' => array ('K' => 'string', 'V' => 'mixed'))));
            $mRule = new TList();
            $mRule->Add($mPrivilegePair);
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
        $mRoleResourcePair = new TPair();
        $mRoleResourcePair->Key = $Resource;
        $mRoleResourcePair->Value = $Role;
        if ($this->FRules->ContainsKey($mRoleResourcePair)) {
            $mPrivilegePair = new TPair();
            $mPrivilegePair->Key = $Privilege;
            $mPrivilegePair->Value = $Assertion;
            if ($this->FRules[$mRoleResourcePair]->Contains($mPrivilegePair)) {
                $this->FRules[$mRoleResourcePair]->Remove($mPrivilegePair);
            }
        }
    }

    /**
     * descHere
     */
    public function __construct() {
        parent::__construct();
        
        TMap::PrepareGeneric(array ('K' => 'string', 'V' => 'array'));
        $this->FResources = new TMap();
        TMap::PrepareGeneric(array ('K' => 'string', 'V' => 'array'));
        $this->FRoles = new TMap();
        TMap::PrepareGeneric(array (
            'K' => array ('TPair' => array ('K' => 'string', 'V' => 'string')), 
            'V' => array (
                'TList' => array (
                    'T' => array (
                        'TPair' => array ('K' => 'string', 'V' => 'mixed'))))));
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
        
        $mParentPath = array ();
        if ($Parent != '') {
            $mParentPath = $this->FResources[$Parent];
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
        
        $mParentPath = array ();
        if ($Parent != '') {
            $mParentPath = $this->FResources[$Parent];
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
     * @return IMap <K: string, V: string[]>
     */
    public function getResources($Resource = '') {
        TType::String($Resource);
        
        if ($Resource == '') {
            return $this->FResources;
        }
        else {
            TMap::PrepareGeneric(array ('K' => 'string', 'V' => 'string'));
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
     * @return IMap <K: string, V: string[]>
     */
    public function getRoles($Role = '') {
        TType::String($Role);
        
        if ($Role == '') {
            return $this->FRoles;
        }
        else {
            TMap::PrepareGeneric(array ('K' => 'string', 'V' => 'string'));
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
        
        $mResourceRolePair = new TPair();
        $mResourceRolePair->Key = $Resource;
        $mResourceRolePair->Value = $Role;
        
        $mPrivilegePair = new TPair();
        $mPrivilegePair->Key = $Privilege;
        $mPrivilegePair->Value = $Assertion;
        
        if (!$this->FRules->ContainsKey($mResourceRolePair)) {
            foreach ($this->FResources[$Resource] as $mAncestorResource) {
                $mResourceRolePair->Key = $mAncestorResource;
                if (($this->FRules->ContainsKey($mResourceRolePair)) && ($this->FRules[$mResourceRolePair]->Contains($mPrivilegePair))) {
                    return true;
                }
            }
            return false;
        }
        else {
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
            TLinkedList::PrepareGeneric(array ('T' => 'string'));
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
            $this->FRole->Clear();
            $this->FRules->Clear();
        }
        else {
            TLinkedList::PrepareGeneric(array ('T' => 'string'));
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
        if ($From == '' && $mPath === array ()) {
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
        if ($From == '' && $mPath === array ()) {
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
 * TAcl
 *
 * @author 许子健
 */
class TAcl extends TObject {
    
    /**
     *
     * @var IAclStorage
     */
    private $FStorage = null;

    /**
     * descHere
     *
     * @param IAclStorage $Storage            
     */
    public function __construct($Storage = null) {
        parent::__construct();
        TType::Object($Storage, 'IAclStorage');
        
        $this->FStorage = $Storage;
    }

    /**
     * descHere
     *
     * @param IAclResource $Resource            
     * @param IAclResource $Parent            
     * @return TAcl
     */
    public function AddResource($Resource, $Parent = null) {
        TType::Object($Resource, 'IAclResource');
        TType::Object($Parent, 'IAclResource');
        
        $mResourceId = $Resource->getResourceId();
        $mParentId = '';
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
     * @param IAclRole $Role            
     * @param IAclRole $Parent            
     * @return TAcl
     */
    public function AddRole($Role, $Parent = null) {
        TType::Object($Role, 'IAclRole');
        TType::Object($Parent, 'IAclRole');
        
        $mRoleId = $Role->getRoleId();
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
     * @param IAclRole $Role            
     * @param IAclResource $Resource            
     * @param string $Privilege            
     * @param mixed $Assertion            
     * @return TAcl
     */
    public function Allow($Role = null, $Resource = null, $Privilege = '', $Assertion = null) {
        TType::Object($Role, 'IAclRole');
        TType::Object($Resource, 'IAclResource');
        TType::String($Privilege);
        
        if ($Privilege == '' && $Assertion === null) {
            throw new EInvalidParameter();
        }
        if ($Privilege != '' && $Assertion !== null) {
            throw new EInvalidParameter();
        }
        if ($Assertion !== null) {
            $mReflection = new ReflectionClass($Assertion);
            if (!$mReflection->implementsInterface('IAclAssertion')) {
                throw new EInvalidParameter();
            }
        }
        
        $mResourceId = '';
        if ($Resource != null) {
            $mResourceId = $Resource->getResourceId();
        }
        if ($Role == null) {
            $mRoles = $this->FStorage->getRoles();
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
     * @param IAclRole $Role            
     * @param IAclResource $Resource            
     * @param string $Privilege            
     * @param mixed $Assertion            
     * @return TAcl
     */
    public function Deny($Role = null, $Resource = null, $Privilege = '', $Assertion = null) {
        TType::Object($Role, 'IAclRole');
        TType::Object($Resource, 'IAclResource');
        TType::String($Privilege);
        
        if ($Privilege == '' && $Assertion === null) {
            throw new EInvalidParameter();
        }
        if ($Privilege != '' && $Assertion !== null) {
            throw new EInvalidParameter();
        }
        if ($Assertion !== null) {
            $mReflection = new ReflectionClass($Assertion);
            if (!$mReflection->implementsInterface('IAclAssertion')) {
                throw new EInvalidParameter();
            }
        }
        
        $mResourceId = '';
        if ($Resource == null) {
            $mResourceId = $Resource->getResourceId();
        }
        if ($Role == null) {
            $mRoles = $this->FStorage->getRoles();
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
     * @return IAclResource
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
     * @param IAclResource $Resource            
     * @return IMap <K: IAclResource, V: IList<T: IAclResource>>
     */
    public function getResources($Resource = null) {
        TType::Object($Resource, 'IAclResource');
        
        $mResourceId = '';
        if ($Resource != null) {
            $mResourceId = $Resource->getResourceId();
            if (!$this->FStorage->HasResource($mResourceId)) {
                throw new EAclNoSuchResource();
            }
        }
        $mRaw = $this->FStorage->getResources($mResourceId);
        TMap::PrepareGeneric(array ('K' => 'IAclResource', 
            'K' => array ('IList' => array ('T' => 'IAclResource'))));
        $mResult = new TMap(true);
        foreach ($mRaw as $mRawResource => $mRawPath) {
            TList::PrepareGeneric(array ('T' => 'TAclResource'));
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
     * @return IMap <K: IAclResource, V: IList<T: IAclResource>>
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
     * @return IAclRole
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
     * @param IAclRole $Role            
     * @return IMap <K: IAclRole, V: IList<T: IAclRole>>
     */
    public function getRoles($Role = null) {
        TType::Object($Role, 'IAclResource');
        
        $mRoleId = '';
        if ($Role != null) {
            $mRoleId = $Role->getRoleId();
            if (!$this->FStorage->HasRole($mRoleId)) {
                throw new EAclNoSuchRole();
            }
        }
        $mRaw = $this->FStorage->getRoles($mRoleId);
        TMap::PrepareGeneric(array ('K' => 'IAclRole', 
            'K' => array ('IList' => array ('T' => 'IAclRole'))));
        $mResult = new TMap(true);
        foreach ($mRaw as $mRawRole => $mRawPath) {
            TList::PrepareGeneric(array ('T' => 'TAclResource'));
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
     * @return IMap <K: IAclRole, V: IList<T: IAclRole>>
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
     * @param IAclResource $Resource            
     * @return boolean
     */
    public function HasResource($Resource) {
        TType::Object($Resource, 'IAclResource');
        
        return $this->FStorage->HasResource($Resource->getResourceId());
    }

    /**
     * descHere
     *
     * @param IAclRole $Role            
     * @return boolean
     */
    public function HasRole($Role) {
        TType::Object($Role, 'IAclRole');
        
        return $this->FStorage->HasRole($Role->getRoleId());
    }

    /**
     * descHere
     *
     * @param IAclRole $Role            
     * @param IAclResource $Resource            
     * @param string $Privilege            
     * @param mixed $Assertion            
     * @return boolean
     */
    public function IsAllowed($Role = null, $Resource = null, $Privilege = '', $Assertion = null) {
        TType::Object($Role, 'IAclRole');
        TType::Object($Resource, 'IAclResource');
        TType::String($Privilege);
        
        if ($Privilege == '' && $Assertion === null) {
            throw new EInvalidParameter();
        }
        if ($Privilege != '' && $Assertion !== null) {
            throw new EInvalidParameter();
        }
        if ($Assertion !== null) {
            $mReflection = new ReflectionClass($Assertion);
            if (!$mReflection->implementsInterface('IAclAssertion')) {
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
     * @param IAclResource $Resource            
     * @return TAcl
     */
    public function RemoveResource($Resource = null) {
        TType::Object($Resource, 'IAclResource');
        
        $mResourceId = '';
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
     * @param IAclRole $Role            
     * @return TAcl
     */
    public function RemoveRole($Role = null) {
        TType::Object($Role, 'IAclRole');
        
        $mRoleId = '';
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
     * @param IAclResource $Resource            
     * @param IAclResource $From            
     * @param boolean $Directly            
     * @return boolean
     */
    public function ResourceInheritsFrom($Resource, $From = null, $Directly = false) {
        TType::Object($Resource, 'IAclResource');
        TType::Object($From, 'IAclResource');
        TType::Bool($Directly);
        
        $mResourceId = $Resource->getResourceId();
        $mFromId = '';
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
     * @param IAclRole $Role            
     * @param IAclRole $From            
     * @param boolean $Directly            
     * @return boolean
     */
    public function RoleInheritsFrom($Role, $From = null, $Directly = false) {
        TType::Object($Role, 'IAclRole');
        TType::Object($From, 'IAclRole');
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