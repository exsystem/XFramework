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
use FrameworkDSW\CoreClasses\IView;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\System\EException;
use FrameworkDSW\System\IDelegate;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\System\TDelegate;
use FrameworkDSW\System\TObject;
use FrameworkDSW\Utilities\TType;

/**
 * Interface TControllerAction
 * @package FrameworkDSW\Controller
 */
interface TControllerAction extends IDelegate {
    /**
     * @param \FrameworkDSW\System\IInterface $Model
     * @return \FrameworkDSW\Containers\IMap <K: string, V: \FrameworkDSW\System\IInterface>
     */
    public function Invoke($Model);
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
     * @param \FrameworkDSW\System\IInterface $Model
     */
    public function Invoke($Model);
}

/**
 * \FrameworkDSW\Controller\TOnSetModelNotify
 *
 * @author 许子健
 */
interface TOnSetModelNotify extends IDelegate {

    /**
     * descHere
     *
     * @param \FrameworkDSW\Controller\TOnModelNotify $OnModelNotify
     */
    public function Invoke($OnModelNotify);
}

/**
 * \FrameworkDSW\Controller\TOnControllerManagerUpdate
 *
 * @author 许子健
 */
interface TOnControllerManagerUpdate extends IDelegate {

    /**
     * descHere
     *
     * @param \FrameworkDSW\Controller\TControllerAction $Action
     */
    public function Invoke($Action);
}

/**
 * \FrameworkDSW\Controller\TOnSetControllerManagerUpdate
 *
 * @author 许子健
 */
interface TOnSetControllerManagerUpdate extends IDelegate {

    /**
     * descHere
     *
     * @param \FrameworkDSW\Controller\TOnControllerManagerUpdate $OnControllerManagerUpdate
     */
    public function Invoke($OnControllerManagerUpdate);
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
     * @param \FrameworkDSW\Controller\TControllerAction $Action
     * @param \FrameworkDSW\System\IInterface $Model
     * @param \FrameworkDSW\Controller\TOnSetControllerManagerUpdate $OnSetControllerManagerUpdate
     * @param \FrameworkDSW\Controller\TOnSetModelNotify $OnSetModelNotify
     */
    public function RegisterModel($Action, $Model, $OnSetControllerManagerUpdate = null, $OnSetModelNotify = null);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Controller\TControllerAction $Action
     * @param \FrameworkDSW\System\IInterface $Model
     * @return boolean
     */
    public function IsModelRegistered($Action, $Model);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Controller\TControllerAction $Action
     * @param \FrameworkDSW\CoreClasses\IView $View
     * @return boolean
     */
    public function IsViewRegistered($Action, $View);

    /**
     * descHere
     *
     * @param \FrameworkDSW\System\IInterface $Model
     */
    public function Notify($Model);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Controller\TControllerAction $Action
     * @param \FrameworkDSW\CoreClasses\IView $View
     */
    public function RegisterView($Action, $View);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Controller\TControllerAction $Action
     */
    public function UnregisterModel($Action);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Controller\TControllerAction $Action
     * @param \FrameworkDSW\CoreClasses\IView $View
     */
    public function UnregisterView($Action, $View);

    /**
     * descHere
     *
     * @param \FrameworkDSW\Controller\TControllerAction $Action
     */
    public function Update($Action);
}

/**
 * ENoSuchActionModelPair
 * @author 许子健
 */
class ENoSuchActionModelPair extends EException {

    /**
     * @var \FrameworkDSW\Controller\TControllerAction
     */
    private $FAction = null;
    /**
     * @var \FrameworkDSW\System\IInterface
     */
    private $FModel = null;

    /**
     * descHere
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param \FrameworkDSW\Controller\TControllerAction $Action
     * @param \FrameworkDSW\System\IInterface $Model
     */
    public function __construct($Message, $Previous = null, $Action, $Model) {
        parent::__construct($Message, $Previous);
        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::Delegate($Action, TControllerAction::class);
        TType::Object($Model, IInterface::class);

        $this->FAction = $Action;
        $this->FModel  = $Model;
    }

    /**
     * descHere
     * @return \FrameworkDSW\Controller\TControllerAction
     */
    public function getAction() {
        return $this->FAction;
    }

    /**
     * descHere
     * @return \FrameworkDSW\System\IInterface
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
     * @var \FrameworkDSW\Controller\TControllerAction
     */
    private $FAction = null;
    /**
     * @var \FrameworkDSW\CoreClasses\IView
     */
    private $FView = null;

    /**
     * descHere
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param \FrameworkDSW\Controller\TControllerAction $Action
     * @param \FrameworkDSW\CoreClasses\IView $View
     */
    public function __construct($Message, $Previous = null, $Action, $View) {
        parent::__construct($Message, $Previous);
        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::Delegate($Action, TControllerAction::class);
        TType::Object($View, IView::class);

        $this->FAction = $Action;
        $this->FView   = $View;
    }

    /**
     * descHere
     * @return \FrameworkDSW\Controller\TControllerAction
     */
    public function getAction() {
        return $this->FAction;
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
 * TODO use delegate to bind actions
 * @author 许子健
 */
class TControllerManager extends TObject implements IControllerManager {
    /**
     *
     * @var \FrameworkDSW\Containers\TMap <K: \FrameworkDSW\Controller\TControllerAction, V: \FrameworkDSW\System\IInterface>
     */
    private $FModelRegistration = null;
    /**
     *
     * @var \FrameworkDSW\Containers\TMap <K: \FrameworkDSW\Controller\TControllerAction, V: \FrameworkDSW\Containers\TLinkedList<T: \FrameworkDSW\Controller\IView>>
     */
    private $FViewRegistration = null;
    /**
     * @var \FrameworkDSW\Containers\TMap <K: \FrameworkDSW\System\IInterface, V: \FrameworkDSW\Containers\TLinkedList<T: \FrameworkDSW\Controller\TControllerAction>>
     */
    private $FNotifierRegistration = null;

    /**
     * descHere
     */
    public function __construct() {
        parent::__construct();
        TMap::PrepareGeneric(['K' => TControllerAction::class, 'V' => IInterface::class]);
        $this->FModelRegistration = new TMap();
        TMap::PrepareGeneric(['K' => TControllerAction::class,
                              'V' => [TLinkedList::class => ['T' => IView::class]]]);
        $this->FViewRegistration = new TMap();
        TMap::PrepareGeneric(['K' => IInterface::class, 'V' => [TLinkedList::class => ['T' => TControllerAction::class]]]);
        $this->FNotifierRegistration = new TMap();
    }

    /**
     * descHere
     */
    public function Destroy() {
        foreach ($this->FViewRegistration as $mRegistration) {
            Framework::Free($mRegistration->Value);
        }
        foreach ($this->FNotifierRegistration as $mRegistration) {
            Framework::Free($mRegistration->Value);
        }

        Framework::Free($this->FModelRegistration);
        Framework::Free($this->FViewRegistration);
        Framework::Free($this->FNotifierRegistration);

        parent::Destroy();
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Controller\TControllerAction $Action
     * @param \FrameworkDSW\System\IInterface $Model
     * @param \FrameworkDSW\Controller\TOnSetControllerManagerUpdate $OnSetControllerManagerUpdate
     * @param \FrameworkDSW\Controller\TOnSetModelNotify $OnSetModelNotify
     */
    public function RegisterModel($Action, $Model, $OnSetControllerManagerUpdate = null, $OnSetModelNotify = null) {
        TType::Delegate($Action, TControllerAction::class);
        TType::Object($Model, IInterface::class);
        TType::Delegate($OnSetModelNotify, TOnSetModelNotify::class);
        TType::Delegate($OnSetControllerManagerUpdate, TOnSetControllerManagerUpdate::class);

        try {
            $this->FModelRegistration[$Action] = $Model;
        }
        catch (ENoSuchKey $Ex) {
            /** @noinspection PhpParamsInspection */
            $this->FModelRegistration->Put($Action, $Model);
        }
        try {
            $this->FNotifierRegistration[$Model][] = $Action;
        }
        catch (ENoSuchKey $Ex) {
            TLinkedList::PrepareGeneric(['T' => TControllerAction::class]);
            /** @noinspection PhpParamsInspection */
            $this->FNotifierRegistration->Put($Model, new TLinkedList(false, [$Action]));
        }
        /** @var TDelegate $OnSetModelNotify */
        if ($OnSetModelNotify !== null) {
            $OnSetModelNotify(Framework::Delegate([$this, 'Notify'], TOnModelNotify::class));
        }
        /** @var TDelegate $OnSetControllerManagerUpdate */
        if ($OnSetControllerManagerUpdate !== null) {
            $OnSetControllerManagerUpdate(Framework::Delegate([$this, 'Update'], TOnControllerManagerUpdate::class));
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Controller\TControllerAction $Action
     * @param \FrameworkDSW\System\IInterface $Model
     * @return boolean
     */
    public function IsModelRegistered($Action, $Model) {
        TType::Delegate($Action, TControllerAction::class);
        TType::Object($Model, IInterface::class);
        /** @noinspection PhpParamsInspection */
        if (!$this->FModelRegistration->ContainsKey($Action)) {
            return false;
        }
        else {
            return $this->FModelRegistration[$Action] === $Model;
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Controller\TControllerAction $Action
     * @param \FrameworkDSW\CoreClasses\IView $View
     * @return boolean
     */
    public function IsViewRegistered($Action, $View) {
        TType::Delegate($Action, TControllerAction::class);
        TType::Object($View, IView::class);
        /** @noinspection PhpParamsInspection */
        if (!$this->FViewRegistration->ContainsKey($Action)) {
            return false;
        }
        else {
            return $this->FModelRegistration[$Action]->Contains($View);
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\System\IInterface $Model
     */
    public function Notify($Model) {
        TType::Object($Model, IInterface::class);
        try {
            foreach ($this->FNotifierRegistration[$Model] as $mAction) {
                if ($mAction($Model)) {
                    $this->Update($mAction);
                }
            }
        }
        catch (ENoSuchKey $Ex) {
            return;
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Controller\TControllerAction $Action
     * @throws ENoSuchActionViewPair
     */
    public function Update($Action) {
        try {
            $mRegistration = $this->FViewRegistration[$Action];
            /** @var TDelegate $Action */
            $mViewData = $Action($this->FModelRegistration[$Action]);
            /** @var IView $mView */
            foreach ($mRegistration as $mView) {
                $mView->Update($mViewData);
            }
            Framework::Free($mViewData);
        }
        catch (ENoSuchKey $Ex) {
            /** @var TControllerAction $Action */
            throw new ENoSuchActionViewPair(sprintf('No such action registered: Update failed.'), $Ex, $Action, null);
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Controller\TControllerAction $Action
     * @param \FrameworkDSW\CoreClasses\IView $View
     */
    public function RegisterView($Action, $View) {
        TType::Delegate($Action, TControllerAction::class);
        TType::Object($View, IView::class);

        try {
            $this->FViewRegistration[$Action]->Add($View);
        }
        catch (ENoSuchKey $Ex) {
            TLinkedList::PrepareGeneric(['T' => IView::class]);
            /** @noinspection PhpParamsInspection */
            $this->FViewRegistration->Put($Action, new TLinkedList(false, [
                $View]));
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Controller\TControllerAction $Action
     * @throws ENoSuchActionModelPair
     */
    public function UnregisterModel($Action) {
        TType::Delegate($Action, TControllerAction::class);

        try {
            /** @noinspection PhpParamsInspection */
            $this->FModelRegistration->Delete($Action);
        }
        catch (ENoSuchKey $Ex) {
            throw new ENoSuchActionModelPair(sprintf('No such action: action was unbind.'), $Ex, $Action, null);
        }
    }

    /**
     * descHere
     *
     * @param \FrameworkDSW\Controller\TControllerAction $Action
     * @param \FrameworkDSW\CoreClasses\IView $View
     * @throws ENoSuchActionViewPair
     */
    public function UnregisterView($Action, $View) {
        TType::Delegate($Action, TControllerAction::class);
        TType::Object($View, IView::class);

        try {
            $this->FViewRegistration[$Action]->Remove($View);
        }
        catch (ENoSuchKey $Ex) {
            throw new ENoSuchActionViewPair(sprintf('No such action view pair: action  is unregistered with the view.'), $Ex, $Action, $View);
        }
    }
}