<?php
/**
 * \FrameworkDSW\Controller\Controller
 * @author  许子健
 * @version $Id$
 * @since   separate file since reversion 52
 */
namespace FrameworkDSW\Controller;

use FrameworkDSW\Containers\TLinkedList;
use FrameworkDSW\Containers\TMap;
use FrameworkDSW\Containers\TPair;
use FrameworkDSW\CoreClasses\IView;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\System\EInvalidParameter;
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
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function Bind($Action, $Model) {
        TType::Object($Model, IModel::class);
        $mController = (string) $Action[0];
        $mAction = (string) $Action[1];

        TType::MetaClass($mController);
        try {
            new \ReflectionMethod($mController, "Action{$mAction}");
        }
        catch (\ReflectionException $Ex) {
            throw new EInvalidParameter();
        }

        if ($this->FBinding->ContainsKey($Action)) {
            $this->FBinding[$Action] = $Model;
        }
        else {
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
                $mController = $mAction[0];
                $mNotifyMethod = "Notify{$mAction[1]}";
                $mNotify = false;
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
     * @param \FrameworkDSW\CoreClasses\IView $View
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function Register($Action, $View) {
        TType::Object($View, IView::class);
        $mController = (string) $Action[0];
        $mAction = (string) $Action[1];

        TType::MetaClass($mController);
        try {
            new \ReflectionMethod($mController, "Action{$mAction}");
        }
        catch (\ReflectionException $Ex) {
            throw new EInvalidParameter();
        }

        if ($this->FRegistration->ContainsKey($Action)) {
            $this->FRegistration[$Action]->Add($View);
        }
        else {
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
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function Unbind($Action, $Model) {
        TType::Object($Model, IModel::class);
        $mController = (string) $Action[0];
        $mAction = (string) $Action[1];

        TType::MetaClass($mController);
        try {
            new \ReflectionMethod($mController, "Action{$mAction}");
        }
        catch (\ReflectionException $Ex) {
            throw new EInvalidParameter();
        }

        $mPair = new TPair();
        $mPair->Key = $Action;
        $mPair->Value = $Model;

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
     * @param \FrameworkDSW\CoreClasses\IView $View
     * @throws \FrameworkDSW\System\EInvalidParameter
     */
    public function Unregister($Action, $View) {
        TType::Object($View, IView::class);
        $mController = (string) $Action[0];
        $mAction = (string) $Action[1];

        TType::MetaClass($mController);
        try {
            new \ReflectionMethod($mController, "Action{$mAction}");
        }
        catch (\ReflectionException $Ex) {
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
     * @throws \FrameworkDSW\System\EInvalidParameter
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
        else {
            throw new EInvalidParameter();
        }
    }
}