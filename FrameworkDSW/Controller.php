<?php
/**
 * \FrameworkDSW\Controller\Controller
 * @author  许子健
 * @version $Id$
 * @since   separate file since reversion 52
 */
namespace FrameworkDSW\Controller;

use FrameworkDSW\Containers\ENoSuchKey;
use FrameworkDSW\Containers\TLinkedList;
use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Containers\TPair;
use FrameworkDSW\CoreClasses\IView;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\System\EException;
use FrameworkDSW\System\IDelegate;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\System\TDelegate;
use FrameworkDSW\System\TObject;
use FrameworkDSW\Utilities\TType;

/**
 * \FrameworkDSW\Controller\TOnControllerUpdate
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
 * \FrameworkDSW\Controller\TOnModelNotify
 *
 * @author 许子健
 */
interface TOnModelNotify extends IDelegate {

    /**
     * descHere
     *
     * @param \FrameworkDSW\Controller\IModel $Model
     */
    public function Invoke($Model);
}

/**
 * \FrameworkDSW\Controller\IModel
 *
 * @author 许子健
 */
interface IModel extends IInterface {

    /**
     * descHere
     *
     * @param \FrameworkDSW\Controller\TOnModelNotify $Value
     */
    public function setNotify($Value);
}

/**
 * \FrameworkDSW\Controller\IController
 *
 * @author 许子健
 */
interface IController extends IInterface {

    /**
     * descHere
     *
     * @param \FrameworkDSW\Controller\TOnControllerUpdate $Value
     */
    public static function setUpdate($Value);
}

/**
 * \FrameworkDSW\Controller\IControllerManager
 *
 * @author 许子健
 */
interface IControllerManager extends IInterface {

    /**
     * descHere
     *
     * @param mixed $Action
     * @param \FrameworkDSW\Controller\IModel $Model
     */
    public function Bind($Action, $Model);

    /**
     * descHere
     *
     * @param mixed $Action
     * @param \FrameworkDSW\Controller\IModel $Model
     * @return boolean
     */
    public function IsBind($Action, $Model);

    /**
     * descHere
     *
     * @param mixed $Action
     * @param \FrameworkDSW\CoreClasses\IView $View
     * @return boolean
     */
    public function IsRegistered($Action, $View);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Controller\IModel $Model
     */
    public function Notify($Model);

    /**
     * descHere
     *
     * @param mixed $Action
     * @param \FrameworkDSW\CoreClasses\IView $View
     */
    public function Register($Action, $View);

    /**
     * descHere
     *
     * @param mixed $Action
     * @param \FrameworkDSW\Controller\IModel $Model
     */
    public function Unbind($Action, $Model);

    /**
     * descHere
     *
     * @param mixed $Action
     * @param \FrameworkDSW\CoreClasses\IView $View
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
 * ENoSuchAction
 * @author 许子健
 */
class ENoSuchAction extends EException {

    /**
     * @var string
     */
    private $FActionName = '';
    /**
     * @var string
     */
    private $FControllerName = '';

    /**
     * descHere
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param string $ControllerName
     * @param string $ActionName
     */
    public function __construct($Message, $Previous = null, $ControllerName, $ActionName) {
        parent::__construct($Message, $Previous);
        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::String($ControllerName);
        TType::String($ActionName);

        $this->FControllerName = $ControllerName;
        $this->FActionName     = $ActionName;
    }

    /**
     * descHere
     * @return string
     */
    public function getActionName() {
        return $this->FActionName;
    }

    /**
     * descHere
     * @return string
     */
    public function getControllerName() {
        return $this->FControllerName;
    }
}

/**
 * ENoSuchActionModelPair
 * @author 许子健
 */
class ENoSuchActionModelPair extends EException {

    /**
     * @var string
     */
    private $FActionName = '';
    /**
     * @var string
     */
    private $FControllerName = '';
    /**
     * @var \FrameworkDSW\Controller\IModel
     */
    private $FModel = null;

    /**
     * descHere
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param string $ControllerName
     * @param string $ActionName
     * @param \FrameworkDSW\Controller\IModel $Model
     */
    public function __construct($Message, $Previous = null, $ControllerName, $ActionName, $Model) {
        parent::__construct($Message, $Previous);
        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::String($ControllerName);
        TType::String($ActionName);
        TType::Object($Model, IModel::class);

        $this->FControllerName = $ControllerName;
        $this->FActionName     = $ActionName;
        $this->FModel          = $Model;
    }

    /**
     * descHere
     * @return string
     */
    public function getActionName() {
        return $this->FActionName;
    }

    /**
     * descHere
     * @return string
     */
    public function getControllerName() {
        return $this->FControllerName;
    }

    /**
     * descHere
     * @return \FrameworkDSW\Controller\IModel
     */
    public function getModel() {
        return $this->FModel;
    }
}

/**
 * ENoSuchActionViewPair
 * @author 许子健
 */
class ENoSuchActionViewPair extends EException {

    /**
     * @var string
     */
    private $FActionName = '';
    /**
     * @var string
     */
    private $FControllerName = '';
    /**
     * @var \FrameworkDSW\CoreClasses\IView
     */
    private $FView = null;

    /**
     * descHere
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param string $ControllerName
     * @param string $ActionName
     * @param \FrameworkDSW\CoreClasses\IView $View
     */
    public function __construct($Message, $Previous = null, $ControllerName, $ActionName, $View) {
        parent::__construct($Message, $Previous);
        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::String($ControllerName);
        TType::String($ActionName);
        TType::Object($View, IView::class);

        $this->FControllerName = $ControllerName;
        $this->FActionName     = $ActionName;
        $this->FView           = $View;
    }

    /**
     * descHere
     * @return string
     */
    public function getActionName() {
        return $this->FActionName;
    }

    /**
     * descHere
     * @return string
     */
    public function getControllerName() {
        return $this->FControllerName;
    }

    /**
     * descHere
     * @return \FrameworkDSW\CoreClasses\IView
     */
    public function getView() {
        return $this->FView;
    }
}

/**
 * \FrameworkDSW\Controller\TControllerManager
 *
 * @author 许子健
 */
class TControllerManager extends TObject implements IControllerManager {

    /**
     *
     * @var \FrameworkDSW\Containers\TMap <K: mixed, V: \FrameworkDSW\Controller\IModel>
     */
    private $FBinding = null;
    /**
     *
     * @var \FrameworkDSW\Containers\TMap <K: mixed, V: \FrameworkDSW\Containers\TLinkedList<T: \FrameworkDSW\Controller\IView>>
     */
    private $FRegistration = null;

    /**
     * descHere
     */
    public function __construct() {
        parent::__construct();
        TMap::PrepareGeneric(['K' => 'array', 'V' => IModel::class]);
        $this->FBinding = new TMap();
        TMap::PrepareGeneric(['K' => 'array',
                              'V' => [TLinkedList::class => ['T' => IView::class]]]);
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
     * @param \FrameworkDSW\Controller\IModel $Model
     * @throws ENoSuchAction
     */
    public function Bind($Action, $Model) {
        TType::Object($Model, IModel::class);
        $mController = (string)$Action[0];
        $mAction     = (string)$Action[1];

        TType::MetaClass($mController);
        try {
            new \ReflectionMethod($mController, "Action{$mAction}");
        }
        catch (\ReflectionException $Ex) {
            throw new ENoSuchAction(sprintf('No such action: action %s in controller %s is undefined. Model binding failed.', $mAction, $mController), null, $mController, $mAction);
        }

        try {
            $this->FBinding[$Action] = $Model;
        }
        catch (ENoSuchKey $Ex) {
            $this->FBinding->Put($Action, $Model);
        }
        $Model->setNotify(new TDelegate([$this, 'Notify'], TOnModelNotify::class));
    }

    /**
     * descHere
     *
     * @param mixed $Action
     * @param \FrameworkDSW\Controller\IModel $Model
     * @return boolean
     */
    public function IsBind($Action, $Model) {
        TType::Object($Model, IModel::class);
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
     * @param \FrameworkDSW\CoreClasses\IView $View
     * @return boolean
     */
    public function IsRegistered($Action, $View) {
        TType::Object($View, IView::class);
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
     * @param \FrameworkDSW\Controller\IModel $Model
     */
    public function Notify($Model) {
        TType::Object($Model, IModel::class);
        foreach ($this->FBinding as $mAction => $mModel) {
            if ($mModel === $Model) {
                $mController   = $mAction[0];
                $mNotifyMethod = "Notify{$mAction[1]}";
                $mNotify       = false;
                if (is_callable([$mController, $mNotifyMethod])) {
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
     * @throws ENoSuchActionViewPair
     */
    public function Update($Action) {
        $mController   = $Action[0];
        $mActionMethod = "Action{$Action[1]}";
        try {
            $mRegistration = $this->FRegistration[$Action];
            $mViewData     = $mController::$mActionMethod($this->FBinding[$Action]);
            foreach ($mRegistration as $mView) {
                $mView->Update($mViewData);
            }
            Framework::Free($mViewData);
        }
        catch (ENoSuchKey $Ex) {
            throw new ENoSuchActionViewPair(sprintf('No such action registered: action %s in controller %s. Update failed.', $Action[1], $Action[0]), $Ex, $Action[0], $Action[1], null);
        }
    }

    /**
     * descHere
     *
     * @param mixed $Action
     * @param \FrameworkDSW\CoreClasses\IView $View
     * @throws ENoSuchAction
     */
    public function Register($Action, $View) {
        TType::Object($View, IView::class);
        $mController = (string)$Action[0];
        $mAction     = (string)$Action[1];

        TType::MetaClass($mController);
        try {
            new \ReflectionMethod($mController, "Action{$mAction}");
        }
        catch (\ReflectionException $Ex) {
            throw new ENoSuchAction(sprintf('No such action: action %s in controller %s is undefined. View registration failed.', $mAction, $mController), null, $mController, $mAction);
        }

        try {
            $this->FRegistration[$Action]->Add($View);
        }
        catch (ENoSuchKey $Ex) {
            TLinkedList::PrepareGeneric(['T' => IView::class]);
            $this->FRegistration->Put($Action, new TLinkedList(false, [
                $View]));
        }
        $mController::setUpdate(new TDelegate([$this, 'Update'], TOnControllerUpdate::class));
    }

    /**
     * descHere
     *
     * @param mixed $Action
     * @param \FrameworkDSW\Controller\IModel $Model
     * @throws ENoSuchAction
     * @throws ENoSuchActionModelPair
     */
    public function Unbind($Action, $Model) {
        TType::Object($Model, IModel::class);
        $mController = (string)$Action[0];
        $mAction     = (string)$Action[1];

        TType::MetaClass($mController);
        try {
            new \ReflectionMethod($mController, "Action{$mAction}");
        }
        catch (\ReflectionException $Ex) {
            throw new ENoSuchAction(sprintf('No such action: action %s in controller %s is undefined. Model unbinding failed.', $mAction, $mController), null, $mController, $mAction);
        }

        $mPair        = new TPair();
        $mPair->Key   = $Action;
        $mPair->Value = $Model;

        try {
            $this->FBinding->Delete($Action);
        }
        catch (ENoSuchKey $Ex) {
            throw new ENoSuchActionModelPair(sprintf('No such action model pair: action %s in controller %s is unbind with the model. Model unbinding failed.', $mAction, $mController), $Ex, $mController, $mAction, $Model);
        }
    }

    /**
     * descHere
     *
     * @param mixed $Action
     * @param \FrameworkDSW\CoreClasses\IView $View
     * @throws ENoSuchAction
     * @throws ENoSuchActionViewPair
     */
    public function Unregister($Action, $View) {
        TType::Object($View, IView::class);
        $mController = (string)$Action[0];
        $mAction     = (string)$Action[1];

        TType::MetaClass($mController);
        try {
            new \ReflectionMethod($mController, "Action{$mAction}");
        }
        catch (\ReflectionException $Ex) {
            throw new ENoSuchAction(sprintf('No such action: action %s in controller %s is undefined. View unregistration failed.', $mAction, $mController), null, $mController, $mAction);
        }

        try {
            $this->FRegistration[$Action]->Remove($View);
        }
        catch (ENoSuchKey $Ex) {
            throw new ENoSuchActionViewPair(sprintf('No such action view pair: action %s in controller %s is unregistered with the view. ', $mAction, $mController), $Ex, $mController, $mAction, $View);
        }
    }
}