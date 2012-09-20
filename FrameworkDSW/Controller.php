<?php
/**
 * Controller.php
 * @author  许子健
 * @version $Id$
 * @since   separate file since reversion 52
 */

require_once 'FrameworkDSW/Containers.php';
require_once 'FrameworkDSW/CoreClasses.php';

/**
 * TOnControllerUpdate
 *
 * @author 许子健
 */
interface TOnControllerUpdate extends IDelegate {

    /**
     * descHere
     *
     * @param mixed $Action
     */
    public function Invoke($Action);
}

/**
 * TOnModelNotify
 *
 * @author 许子健
 */
interface TOnModelNotify extends IDelegate {

    /**
     * descHere
     *
     * @param IModel $Model
     */
    public function Invoke($Model);
}

/**
 * IModel
 *
 * @author 许子健
 */
interface IModel extends IInterface {

    /**
     * descHere
     *
     * @param TOnModelNotify $Value
     */
    public function setNotify($Value);
}

/**
 * IController
 *
 * @author 许子健
 */
interface IController extends IInterface {

    /**
     * descHere
     *
     * @param TOnControllerUpdate $Value
     */
    public static function setUpdate($Value);
}

/**
 * IControllerManager
 *
 * @author 许子健
 */
interface IControllerManager extends IInterface {

    /**
     * descHere
     *
     * @param mixed $Action
     * @param IModel $Model
     */
    public function Bind($Action, $Model);

    /**
     * descHere
     *
     * @param mixed $Action
     * @param IModel $Model
     * @return boolean
     */
    public function IsBind($Action, $Model);

    /**
     * descHere
     *
     * @param mixed $Action
     * @param IView $View
     * @return boolean
     */
    public function IsRegistered($Action, $View);

    /**
     * descHere
     *
     * @param IModel $Model
     */
    public function Notify($Model);

    /**
     * descHere
     *
     * @param mixed $Action
     * @param IView $View
     */
    public function Register($Action, $View);

    /**
     * descHere
     *
     * @param mixed $Action
     * @param IModel $Model
     */
    public function Unbind($Action, $Model);

    /**
     * descHere
     *
     * @param mixed $Action
     * @param IView $View
     */
    public function Unregister($Action, $View);

    /**
     * descHere
     *
     * @param mixed $Action
     */
    public function Update($Action);
}

/**
 * TControllerManager
 *
 * @author 许子健
 */
class TControllerManager extends TObject implements IControllerManager {

    /**
     *
     * @var TMap <K: mixed, V: IModel>
     */
    private $FBinding = null;
    /**
     *
     * @var TMap <K: mixed, V: TLinkedList<T: IView>>
     */
    private $FRegistration = null;

    /**
     * descHere
     */
    public function __construct() {
        parent::__construct();
        TMap::PrepareGeneric(array ('K' => 'array', 'V' => 'IModel'));
        $this->FBinding = new TMap();
        TMap::PrepareGeneric(array ('K' => 'array',
            'V' => array ('TLinkedList' => array ('T' => 'IView'))));
        $this->FRegistration = new TMap();
    }

    /**
     * descHere
     */
    public function Destroy() {
        foreach ($this->FRegistration as $mRegistration) {
            Framework::Free($mRegistration->Value);
        }

        Framework::Free($this->FBinding);
        Framework::Free($this->FRegistration);

        parent::Destroy();
    }

    /**
     * descHere
     *
     * @param mixed $Action
     * @param IModel $Model
     */
    public function Bind($Action, $Model) {
        TType::Object($Model, 'IModel');
        $mController = (string) $Action[0];
        $mAction = (string) $Action[1];

        TType::MetaClass($mController);
        try {
            $mActionMeta = new ReflectionMethod($mController, "Action{$mAction}");
        }
        catch (ReflectionException $Ex) {
            throw new EInvalidParameter();
        }

        if ($this->FBinding->ContainsKey($Action)) {
            $this->FBinding[$Action] = $Model;
        }
        else {
            $this->FBinding->Put($Action, $Model);
        }
        $Model->setNotify(new TDelegate(array ($this, 'Notify'), 'TOnModelNotify'));
    }

    /**
     * descHere
     *
     * @param mixed $Action
     * @param IModel $Model
     * @return boolean
     */
    public function IsBind($Action, $Model) {
        TType::Object($Model, 'IModel');
        if (!$this->FBinding->ContainsKey($Action)) {
            return false;
        }
        else {
            return $this->FBinding[$Action] == $Model;
        }
    }

    /**
     * descHere
     *
     * @param mixed $Action
     * @param IView $View
     * @return boolean
     */
    public function IsRegistered($Action, $View) {
        TType::Object($View, 'IView');
        if (!$this->FRegistration->ContainsKey($Action)) {
            return false;
        }
        else {
            return $this->FBinding[$Action]->Contains($View);
        }
    }

    /**
     * descHere
     *
     * @param IModel $Model
     */
    public function Notify($Model) {
        TType::Object($Model, 'IModel');
        foreach ($this->FBinding as $mAction => $mModel) {
            if ($mModel == $Model) {
                $mController = $mAction[0];
                $mNotifyMethod = "Notify{$mAction[1]}";
                $mNotify = false;
                if (is_callable(array ($mController, $mNotifyMethod))) {
                    $mNotify = $mController::$mNotifyMethod($Model);
                }
                if ($mNotify) {
                    $this->Update($mAction);
                }
            }
        }
    }

    /**
     * descHere
     *
     * @param mixed $Action
     * @param IView $View
     */
    public function Register($Action, $View) {
        TType::Object($View, 'IView');
        $mController = (string) $Action[0];
        $mAction = (string) $Action[1];

        TType::MetaClass($mController);
        try {
            $mActionMeta = new ReflectionMethod($mController, "Action{$mAction}");
        }
        catch (ReflectionException $Ex) {
            throw new EInvalidParameter();
        }

        if ($this->FRegistration->ContainsKey($Action)) {
            $this->FRegistration[$Action]->Add($View);
        }
        else {
            TLinkedList::PrepareGeneric(array ('T' => 'IView'));
            $this->FRegistration->Put($Action, new TLinkedList(false, array (
                $View)));
        }
        $mController::setUpdate(new TDelegate(array ($this, 'Update'), 'TOnControllerUpdate'));
    }

    /**
     * descHere
     *
     * @param mixed $Action
     * @param IModel $Model
     */
    public function Unbind($Action, $Model) {
        TType::Object($Model, 'IModel');
        $mController = (string) $Action[0];
        $mAction = (string) $Action[1];

        TType::MetaClass($mController);
        try {
            $mActionMeta = new ReflectionMethod($mController, "Action{$mAction}");
        }
        catch (ReflectionException $Ex) {
            throw new EInvalidParameter();
        }

        $mPair = new TPair();
        $mPair = $Action;
        $mPair = $Model;

        if ($this->FBinding->Contains($mPair)) {
            $this->FBinding->Delete($Action);
        }
        else {
            throw new EInvalidParameter();
        }
    }

    /**
     * descHere
     *
     * @param mixed $Action
     * @param IView $View
     */
    public function Unregister($Action, $View) {
        TType::Object($View, 'IView');
        $mController = (string) $Action[0];
        $mAction = (string) $Action[1];

        TType::MetaClass($mController);
        try {
            $mActionMeta = new ReflectionMethod($mController, "Action{$mAction}");
        }
        catch (ReflectionException $Ex) {
            throw new EInvalidParameter();
        }

        if ($this->FRegistration->ContainsKey($Action)) {
            $this->FRegistration[$Action]->Remove($View);
        }
        else {
            throw new EInvalidParameter();
        }
    }

    /**
     * descHere
     *
     * @param mixed $Action
     */
    public function Update($Action) {
        $mController = $Action[0];
        $mActionMethod = "Action{$Action[1]}";
        if ($this->FRegistration->ContainsKey($Action)) {
            $mRegistration = $this->FRegistration[$Action];
            $mViewData = $mController::$mActionMethod($this->FBinding[$Action]);
            foreach ($mRegistration as $mView) {
                $mView->Update($mViewData);
            }
            Framework::Free($mViewData);
        }
    }
}