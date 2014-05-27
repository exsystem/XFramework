<?php
/**
 * \FrameworkDSW\CoreClasses
 * @author    许子健
 * @version    $Id$
 * @since    separate file since reversion 1
 */
namespace FrameworkDSW\CoreClasses;

use FrameworkDSW\Containers\EIndexOutOfBounds;
use FrameworkDSW\Containers\TList;
use FrameworkDSW\Framework\Framework;
use FrameworkDSW\System\EException;
use FrameworkDSW\System\IDelegate;
use FrameworkDSW\System\IInterface;
use FrameworkDSW\System\TEnum;
use FrameworkDSW\System\TObject;
use FrameworkDSW\System\TSet;
use FrameworkDSW\Utilities\TSize;
use FrameworkDSW\Utilities\TType;

class EComponentErr extends EException {
}

class EInvalidComponentName extends EComponentErr {
    /**
     * @var string
     */
    private $FComponentName = '';

    /**
     * @param string $Message
     * @param \FrameworkDSW\System\EException $Previous
     * @param string $ComponentName
     */
    public function __construct($Message, $Previous = null, $ComponentName) {
        parent::__construct($Message, $Previous);
        TType::String($Message);
        TType::Object($Previous, EException::class);
        TType::String($ComponentName);

        $this->FComponentName = $ComponentName;
    }

    public function getComponentName() {
        return $this->FComponentName;
    }
}

class ENoSuchComponent extends EComponentErr {
}

class ENoOwnerComponent extends EComponentErr {
}

/**
 * \FrameworkDSW\CoreClasses\TOperation
 * @author    许子健
 */
final class TOperation extends TEnum {
    const eInsert = 0, eRemove = 1;
}

/**
 * \FrameworkDSW\CoreClasses\TCursor
 * @author    许子健
 */
final class TCursor extends TEnum {
    const eDefault   = 0;
    const eNone      = -1;
    const eArrow     = -2;
    const eCross     = -3;
    const eIBeam     = -4;
    const eSize      = -22;
    const eSizeNESW  = -6;
    const eSizeNS    = -7;
    const eSizeNWSE  = -8;
    const eSizeWE    = -9;
    const eUpArrow   = -10;
    const eHourGlass = -11;
    const eDrag      = -12;
    const eNoDrag    = -13;
    const eHSplit    = -14;
    const eVSplit    = -15;
    const eMultiDrag = -16;
    const eSQLWait   = -17;
    const eNo        = -18;
    const eAppStart  = -19;
    const eHelp      = -20;
    const eHandPoint = -21;
    const eSizeAll   = -22;
}

/**
 * \FrameworkDSW\CoreClasses\TDragState
 * @author    许子健
 */
final class TDragState extends TEnum {
    const eDragEnter = 0, eDragLeave = 1, eDragMove = 2;
}

/**
 * \FrameworkDSW\CoreClasses\TDragDropObject
 * @author    许子健
 */
class TDragDropObject extends TObject { //TODO: impl TDragDropObject.
}

/**
 * \FrameworkDSW\CoreClasses\TShiftState
 * @author    许子健
 */
final class TShiftState extends TSet {
    const eShift  = 0, eAlt = 1, eCtrl = 2;
    const eLeft   = 3, eRight = 4, eMiddle = 5;
    const eDouble = 6, eTouch = 7, ePen = 8;
}

/**
 * \FrameworkDSW\CoreClasses\TMouseButton
 * @author    许子健
 */
final class TMouseButton extends TSet {
    const eLeft = 0, eRight = 1, eMiddle = 2;
}

/**
 *
 * @author    许子健
 */
final class TAlign extends TEnum {
    const eNone       = 0;
    const eTopLeft    = 1, eTop = 2, eTopRight = 3;
    const eLeft       = 4, eClient = 5, eRight = 6;
    const eBottomLeft = 7, eBottom = 8, eBottomRight = 9;
}

//TODO: think about IAlign.


/**
 * \FrameworkDSW\CoreClasses\TNotifyEvent
 * @author    许子健
 */
interface TNotifyEvent extends IDelegate {

    /**
     *
     * @param    \FrameworkDSW\System\TObject $Sender
     */
    public function Invoke($Sender);
}

/**
 * \FrameworkDSW\CoreClasses\TMouseEvent
 * @author    许子健
 */
interface TMouseEvent extends IDelegate {

    /**
     *
     * @param    \FrameworkDSW\System\TObject $Sender
     * @param    \FrameworkDSW\CoreClasses\TMouseButton $Button
     * @param    \FrameworkDSW\CoreClasses\TShiftState $Shift
     * @param    integer $X
     * @param    integer $Y
     */
    public function Invoke($Sender, $Button, $Shift, $X, $Y);
}

/**
 * \FrameworkDSW\CoreClasses\TContextPopupEvent
 * @author    许子健
 */
interface TContextPopupEvent extends IDelegate {

    /**
     *
     * @param    \FrameworkDSW\System\TObject $Sender
     * @param    integer $X
     * @param    integer $Y
     * @param    boolean $Handled
     */
    public function Invoke($Sender, $X, $Y, &$Handled);
}

/**
 * \FrameworkDSW\CoreClasses\TDragOverEvent
 * @author 许子健
 */
interface TDragOverEvent extends IDelegate {

    /**
     *
     * @param    \FrameworkDSW\System\TObject $Sender
     * @param    \FrameworkDSW\System\TObject $Source
     * @param    integer $X
     * @param    integer $Y
     * @param    \FrameworkDSW\CoreClasses\TDragState $State
     * @param    boolean $Accept
     */
    public function Invoke($Sender, $Source, $X, $Y, $State, &$Accept);
}

/**
 * \FrameworkDSW\CoreClasses\TDragDropEvent
 * @author    许子健
 */
interface TDragDropEvent extends IDelegate {

    /**
     *
     * @param    \FrameworkDSW\System\TObject $Sender
     * @param    \FrameworkDSW\CoreClasses\TDragDropObject $Source
     * @param    integer $X
     * @param    integer $Y
     */
    public function Invoke($Sender, $Source, $X, $Y);
}

/**
 *
 * @author    许子健
 */
interface TStartDragEvent extends IDelegate {

    /**
     *
     * @param    \FrameworkDSW\System\TObject $Sender
     * @param    \FrameworkDSW\CoreClasses\TDragState $DragObject
     */
    public function Invoke($Sender, &$DragObject);
}

/**
 * \FrameworkDSW\CoreClasses\TEndDragEvent
 * @author    许子健
 */
interface TEndDragEvent extends IDelegate {

    /**
     *
     * @param    \FrameworkDSW\System\TObject $Sender
     * @param    \FrameworkDSW\System\TObject $Target
     * @param    integer $X
     * @param    integer $Y
     */
    public function Invoke($Sender, $Target, $X, $Y);
}

/**
 * \FrameworkDSW\CoreClasses\TMouseWheelEvent
 * @author    许子健
 */
interface TMouseWheelEvent extends IDelegate {

    /**
     *
     * @param    \FrameworkDSW\System\TObject $Sender
     * @param    \FrameworkDSW\CoreClasses\TShiftState $Shift
     * @param    integer $WheelDelta
     * @param    integer $X
     * @param    integer $Y
     * @param    boolean $Handled
     */
    public function Invoke($Sender, $Shift, $WheelDelta, $X, $Y, &$Handled);
}

/**
 * \FrameworkDSW\CoreClasses\TMouseWheelUpDownEvent
 * @author    许子健
 */
interface TMouseWheelUpDownEvent extends IDelegate {

    /**
     *
     * @param    \FrameworkDSW\System\TObject $Sender
     * @param    \FrameworkDSW\CoreClasses\TShiftState $Shift
     * @param    integer $X
     * @param    integer $Y
     * @param    boolean $Handled
     */
    public function Invoke($Sender, $Shift, $X, $Y, &$Handled);
}

/**
 * \FrameworkDSW\CoreClasses\IView
 * @author    许子健
 */
interface IView extends IInterface {
    /**
     * descHere
     * @param \FrameworkDSW\Containers\IMap $ViewData <K: string, V: \FrameworkDSW\System\IInterface>
     */
    public function Update($ViewData);
}

/**
 * \FrameworkDSW\CoreClasses\TComponent class
 * @author 许子健
 */
abstract class TComponent extends TObject {
    /**
     *
     * @var string
     */
    const CValidIdentPattern = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/';

    /**
     *
     * @var string
     */
    private $FName = '';
    /**
     * @var mixed
     */
    private $FTag = null;
    /**
     *
     * @var \FrameworkDSW\CoreClasses\TComponent
     */
    private $FOwner = null;
    /**
     *
     * @var \FrameworkDSW\Containers\TList <T: \FrameworkDSW\CoreClasses\TComponent>
     */
    private $FComponents = null; //TODO: what about using a linked list?


    /**
     * @param \FrameworkDSW\CoreClasses\TComponent $Owner
     * @see FrameworkDSW/TObject#Create()
     */
    public function __construct($Owner = null) {
        parent::__construct();
        TType::Object($Owner, TComponent::class);

        if ($Owner !== null) {
            $Owner->InsertComponent($this);
        }
    }

    /**
     * (non-PHPdoc)
     * @see FrameworkDSW/TObject#Destroy()
     */
    public function Destroy() {
        if ($this->FOwner !== null) {
            $this->FOwner->RemoveComponent($this);
        }

        parent::Destroy();
    }

    /**
     *
     * @param string $Name
     * @throws EInvalidComponentName
     */
    private static function ValidateName($Name) {
        if (preg_match(self::CValidIdentPattern, $Name) != 1) {
            throw new EInvalidComponentName(sprintf('Invalid component name: %s.', $Name), null, $Name);
        }
    }

    /**
     *
     * @param \FrameworkDSW\CoreClasses\TComponent $Component
     */
    private function Insert($Component) {
        if ($this->FComponents === null) {
            TList::PrepareGeneric(['T' => TComponent::class]);
            $this->FComponents = new TList();
        }
        $this->FComponents->Add($Component);
        $Component->FOwner = $this;
    }

    /**
     *
     * @param \FrameworkDSW\CoreClasses\TComponent $Component
     */
    private function Remove($Component) {
        $Component->FOwner = null;
        $this->FComponents->Remove($Component);
        if ($this->FComponents->Size() == 0) {
            Framework::Free($this->FComponents);
        }
    }

    /**
     *
     * @param \FrameworkDSW\CoreClasses\TComponent $Component
     * @param \FrameworkDSW\CoreClasses\TOperation $Operation
     */
    protected function Notify($Component, $Operation) {
        TType::Object($Component, TComponent::class);
        TType::Object($Operation, TOperation::class);

        if ($this->FComponents !== null) {
            /**@var TComponent $mComponent */
            foreach ($this->FComponents as $mComponent) {
                $mComponent->Notify($Component, $Operation);
            }
        }
    }

    /**
     *
     * @param \FrameworkDSW\CoreClasses\TComponent $Component
     */
    public function InsertComponent($Component) {
        TType::Object($Component, TComponent::class);

        if ($Component->FOwner !== null) {
            $Component->FOwner->Remove($Component);
        }
        $this->Insert($Component);
        $this->Notify($Component, TOperation::eInsert());
    }

    /**
     *
     * @param \FrameworkDSW\CoreClasses\TComponent $Component
     */
    public function RemoveComponent($Component) {
        TType::Object($Component, TComponent::class);

        $this->Notify($Component, TOperation::eRemove());
        $this->Remove($Component);
    }

    /**
     *
     * @param string $Name
     * @throws ENoSuchComponent
     * @return \FrameworkDSW\CoreClasses\TComponent
     */
    public function FindComponent($Name) {
        TType::String($Name);

        self::ValidateName($Name);
        if ($this->FComponents !== null) {
            /**@var TComponent $mComponent */
            foreach ($this->FComponents as $mComponent) {
                if ($mComponent->getName() == $Name) {
                    return $mComponent;
                }
            }
        }

        throw new ENoSuchComponent(sprintf('No such component: %s.', $Name));
    }

    /**
     *
     * @return    string
     */
    public function getName() {
        return $this->FName;
    }

    /**
     *
     * @param string $Value
     * @throws EInvalidComponentName
     */
    public function setName($Value) {
        TType::String($Value);

        if ($Value != $this->FName) {
            if ($Value == '') {
                $this->FName = '';

                return;
            }

            if ($this->FOwner === null) {
                self::ValidateName($Value);
                $this->FName = $Value;
            }
            else {
                try {
                    $this->FOwner->FindComponent($Value);
                    throw new EInvalidComponentName(sprintf('Invalid component name: %s.', $Value), null, $Value);
                }
                catch (ENoSuchComponent $e) {
                    $this->FName = $Value;
                }
            }
        }
    }

    /**
     *
     * @return \FrameworkDSW\CoreClasses\TComponent
     */
    public function getOwner() {
        return $this->FOwner;
    }

    /**
     *
     * @param integer $Index
     * @throws \FrameworkDSW\Containers\EIndexOutOfBounds
     * @return \FrameworkDSW\CoreClasses\TComponent
     */
    public function getComponent($Index) {
        if ($this->FComponents === null) {
            throw new EIndexOutOfBounds();
        }

        return $this->FComponents[$Index];
    }

    /**
     *
     * @return integer
     */
    public function getComponentCount() {
        if ($this->FComponents === null) {
            return 0;
        }

        return $this->FComponents->Size();
    }

    /**
     *
     * @throws ENoOwnerComponent
     * @return integer
     */
    public function getComponentIndex() {
        if ($this->FOwner === null) {
            throw new ENoOwnerComponent();
        }

        return $this->FOwner->FComponents->IndexOf($this);
    }

    /**
     * Return the tag.
     * @return mixed
     */
    public function getTag() {
        return $this->FTag;
    }

    /**
     * Set the tag.
     * @param mixed $Value
     */
    public function setTag($Value) {
        $this->FTag = $Value;
    }
}

//TODO: TComponentState


/**
 * \FrameworkDSW\CoreClasses\IControl
 * @author    许子健
 */
interface IControl extends IInterface {

    /**
     * tell the layout manager to do layout fot this control.
     */
    public function Layout();

    /**
     * Invalidate
     */
    public function Invalidate();

    /**
     * Repaint
     */
    public function Repaint();

    /**
     * Update :to invoke Parent.Update() if Parent not null
     */
    public function Update();

    /**
     *
     * @return    string
     */
    public function getName();

    /**
     *
     * @param    string $Value
     */
    public function setName($Value);

    /**
     *
     * @return    integer
     */
    public function getLeft();

    /**
     *
     * @return    integer
     */
    public function getTop();

    /**
     *
     * @return    integer
     */
    public function getWidth();

    /**
     *
     * @return    integer
     */
    public function getHeight();

    /**
     *
     * @return    TPoint
     */
    public function getPosition();

    /**
     *
     * @param    TPoint $Value
     */
    public function setPosition($Value);

    /**
     *
     * @return    TSize
     */
    public function getSize();

    /**
     *
     * @param    TSize $Value
     */
    public function setSize($Value);

    /**
     *
     * @return    boolean
     */
    public function getVisible();

    /**
     *
     * @param    boolean $Value
     */
    public function setVisible($Value);

    /**
     *
     * @return    boolean
     */
    public function getEnabled();

    /**
     *
     * @param    boolean $Value
     */
    public function setEnabled($Value);

    /**
     *
     * @return    IControl
     */
    public function getParent();

    /**
     *
     * @param    IControl $Value
     */
    public function setParent($Value);

    /**
     *
     * @return    boolean
     */
    public function getParentFont();

    /**
     *
     * @param    boolean $Value
     */
    public function setParentFont($Value);

    /**
     *
     * @return    boolean
     */
    public function getParentColor();

    /**
     *
     * @param    boolean $Value
     */
    public function setParentColor($Value);

    /**
     *
     * @return    TFont
     */
    public function getFont();

    /**
     *
     * @param    TFont $Value
     */
    public function setFont($Value);

    /**
     *
     * @return    TColor
     */
    public function getColor();

    /**
     *
     * @param    TColor $Value
     */
    public function setColor($Value);

    /**
     *
     * @return    string
     */
    public function getText();

    /**
     *
     * @param    string $Value
     */
    public function setText($Value);

    /**
     *
     * @return    TCursor
     */
    public function getCursor();

    /**
     *
     * @param    TCursor $Value
     */
    public function setCursor($Value);

    /**
     *
     * @return    TCursor
     */
    public function getDragCursor();

    /**
     *
     * @param    TCursor $Value
     */
    public function setDragCursor($Value);

    /**
     *
     * @return    IPopupMenu
     */
    public function getPopupMenu();

    /**
     *
     * @param    IPopupMenu $Value
     */
    public function setPopupMenu($Value);

    /**
     *
     * @return    boolean
     */
    public function getParentShowHint();

    /**
     *
     * @param    boolean $Value
     */
    public function setParentShowHint($Value);

    /**
     *
     * @return    boolean
     */
    public function getShowHint();

    /**
     *
     * @param    boolean $Value
     */
    public function setShowHint($Value);

    /**
     *
     * @return    string
     */
    public function getHint();

    /**
     *
     * @param    string $Value
     */
    public function setHint($Value);

    /**
     *
     * @return    TDelegate
     */
    public function getOnClick();

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnClick($Value);

    /**
     *
     * @return    TDelegate
     */
    public function getOnContextPopup();

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnContextPopup($Value);

    /**
     *
     * @return    TDelegate
     */
    public function getOnDoubleClick();

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnDoubleClick($Value);

    /**
     *
     * @return    TDelegate
     */
    public function getOnDragDrop();

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnDragDrop($Value);

    /**
     *
     * @return    TDelegate
     */
    public function getOnDragOver();

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnDragOver($Value);

    /**
     *
     * @return    TDelegate
     */
    public function getOnStartDrag();

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnStartDrag($Value);

    /**
     *
     * @return    TDelegate
     */
    public function getOnEndDrag();

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnEndDrag($Value);

    /**
     *
     * @return    TDelegate
     */
    public function getOnMouseDown();

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnMouseDown($Value);

    /**
     *
     * @return    TDelegate
     */
    public function getOnMouseEnter();

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnMouseEnter($Value);

    /**
     *
     * @return    TDelegate
     */
    public function getOnMouseLeave();

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnMouseLeave($Value);

    /**
     *
     * @return    TDelegate
     */
    public function getOnMouseMove();

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnMouseMove($Value);

    /**
     *
     * @return    TDelegate
     */
    public function getOnMouseUp();

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnMouseUp($Value);

    /**
     *
     * @return    TDelegate
     */
    public function getOnMouseWheel();

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnMouseWheel($Value);

    /**
     *
     * @return    TDelegate
     */
    public function getOnMouseWheelDown();

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnMouseWheelDown($Value);

    /**
     *
     * @return    TDelegate
     */
    public function getOnMouseWheelUp();

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnMouseWheelUp($Value);

    /**
     *
     * @return    TDelegate
     */
    public function getOnResize();

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnResize($Value);
}

/**
 *
 * @author    许子健
 */
abstract class TControl extends TComponent implements IControl {
    /**
     *
     * @var    TControl
     */
    private $FParent = null;
    /**
     *
     * @var    integer
     */
    private $FLeft = 0;
    /**
     *
     * @var    integer
     */
    private $FTop = 0;
    /**
     *
     * @var    integer
     */
    private $FWidth = 0;
    /**
     *
     * @var    integer
     */
    private $FHeight = 0;
    /**
     *
     * @var    boolean
     */
    private $FVisible = false;
    /**
     *
     * @var    boolean
     */
    private $FEnabled = false;
    /**
     *
     * @var    boolean
     */
    private $FParentFont = false;
    /**
     *
     * @var    boolean
     */
    private $FParentColor = false;
    /**
     *
     * @var    TAlign
     */
    private $FAlign = null;
    /**
     *
     * @var    boolean
     */
    private $FAutoSize = false;
    /**
     *
     * @var    TDragMode
     */
    private $FDragMode = null;
    /**
     *
     * @var    TFont
     */
    private $FFont = null;
    /**
     *
     * @var    TColor
     */
    private $FColor = null;
    /**
     *
     * @var    TCursor
     */
    private $FCursor = null;
    /**
     *
     * @var    TCursor
     */
    private $FDragCursor = null;
    /**
     *
     * @var    IPopMenu
     */
    private $FPopupMenu = null;
    /**
     *
     * @var    string
     */
    private $FHint = '';
    /**
     *
     * @var    boolean
     */
    private $FShowHint = false;
    /**
     *
     * @var    boolean
     */
    private $FParentHint = false;
    /**
     *
     * @var    TDragKind
     */
    private $FDragKind = null;
    /**
     *
     * @var    TDelegate
     */
    private $FOnMouseDown = null;
    /**
     *
     * @var    TDelegate
     */
    private $FOnMouseMove = null;
    /**
     *
     * @var    TDelegate
     */
    private $FOnMouseUp = null;
    /**
     *
     * @var    TDelegate
     */
    private $FOnDragDrop = null;
    /**
     *
     * @var    TDelegate
     */
    private $FOnDragOver = null;
    /**
     *
     * @var    TDelegate
     */
    private $FOnResize = null;
    /**
     *
     * @var    TDelegate
     */
    private $FOnStartDrag = null;
    /**
     *
     * @var    TDelegate
     */
    private $FOnEndDrag = null;
    /**
     *
     * @var    TDelegate
     */
    private $FOnClick = null;
    /**
     *
     * @var    TDelegate
     */
    private $FOnDoubleClick = null;
    /**
     *
     * @var    TDelegate
     */
    private $FOnContextPopup = null;
    /**
     *
     * @var    TDelegate
     */
    private $FOnMouseLeave = null;
    /**
     *
     * @var    TDelegate
     */
    private $FOnMouseEnter = null;
    /**
     *
     * @var    TDelegate
     */
    private $FOnMouseWheel = null;
    /**
     *
     * @var    TDelegate
     */
    private $FOnMouseWheelDown = null;
    /**
     *
     * @var    TDelegate
     */
    private $FOnMouseWheelUp = null;

    /**
     *
     */
    protected function slotClick() {
        if (isset($this->FOnClick)) {
            $this->FOnClick($this);
        }
    }

    /**
     *
     */
    protected function slotDoubleClick() {
        if (isset($this->FOnDoubleClick)) {
            $this->FOnDoubleClick($this);
        }
    }

    /**
     *
     * @param    TMouseButton $Button
     * @param    TShiftState $Shift
     * @param    integer $X
     * @param    integer $Y
     */
    protected function slotMouseDown($Button, $Shift, $X, $Y) {
        if (isset($this->FOnMouseDown)) {
            $this->FOnMouseDown($this, $Button, $Shift, $X, $Y);
        }
    }

    /**
     *
     * @param    TMouseButton $Button
     * @param    TShiftState $Shift
     * @param    integer $X
     * @param    integer $Y
     */
    protected function slotMouseUp($Button, $Shift, $X, $Y) {
        if (isset($this->FOnMouseUp)) {
            $this->FOnMouseUp($this, $Button, $Shift, $X, $Y);
        }
    }

    /**
     *
     * @param    TMouseButton $Button
     * @param    TShiftState $Shift
     * @param    integer $X
     * @param    integer $Y
     */
    protected function slotMouseMove($Button, $Shift, $X, $Y) {
        if (isset($this->FOnMouseMove)) {
            $this->FOnMouseMove($this, $Button, $Shift, $X, $Y);
        }
    }

    /**
     * tell the layout manager to do layout fot this control.
     */
    public function Layout() {
    }

    /**
     * Invalidate
     */
    public function Invalidate() {
    }

    /**
     * Repaint
     */
    public function Repaint() {
    }

    /**
     * Update :to invoke Parent.Update() if Parent not null
     */
    public function Update() {
    }

    /**
     *
     * @return    string
     */
    public function getName() {
    }

    /**
     *
     * @param    string $Value
     */
    public function setName($Value) {
    }

    /**
     *
     * @return    integer
     */
    public function getLeft() {
    }

    /**
     *
     * @return    integer
     */
    public function getTop() {
    }

    /**
     *
     * @return    integer
     */
    public function getWidth() {
    }

    /**
     *
     * @return    integer
     */
    public function getHeight() {
    }

    /**
     *
     * @return    TPoint
     */
    public function getPosition() {
    }

    /**
     *
     * @param    TPoint $Value
     */
    public function setPosition($Value) {
    }

    /**
     *
     * @return    TSize
     */
    public function getSize() {
    }

    /**
     *
     * @param    TSize $Value
     */
    public function setSize($Value) {
    }

    /**
     *
     * @return    boolean
     */
    public function getVisible() {
    }

    /**
     *
     * @param    boolean $Value
     */
    public function setVisible($Value) {
    }

    /**
     *
     * @return    boolean
     */
    public function getEnabled() {
    }

    /**
     *
     * @param    boolean $Value
     */
    public function setEnabled($Value) {
    }

    /**
     *
     * @return    IControl
     */
    public function getParent() {
    }

    /**
     *
     * @param    IControl $Value
     */
    public function setParent($Value) {
    }

    /**
     *
     * @return    boolean
     */
    public function getParentFont() {
    }

    /**
     *
     * @param    boolean $Value
     */
    public function setParentFont($Value) {
    }

    /**
     *
     * @return    boolean
     */
    public function getParentColor() {
    }

    /**
     *
     * @param    boolean $Value
     */
    public function setParentColor($Value) {
    }

    /**
     *
     * @return    TFont
     */
    public function getFont() {
    }

    /**
     *
     * @param    TFont $Value
     */
    public function setFont($Value) {
    }

    /**
     *
     * @return    TColor
     */
    public function getColor() {
    }

    /**
     *
     * @param    TColor $Value
     */
    public function setColor($Value) {
    }

    /**
     *
     * @return    string
     */
    public function getText() {
    }

    /**
     *
     * @param    string $Value
     */
    public function setText($Value) {
    }

    /**
     *
     * @return    TCursor
     */
    public function getCursor() {
    }

    /**
     *
     * @param    TCursor $Value
     */
    public function setCursor($Value) {
    }

    /**
     *
     * @return    TCursor
     */
    public function getDragCursor() {
    }

    /**
     *
     * @param    TCursor $Value
     */
    public function setDragCursor($Value) {
    }

    /**
     *
     * @return    IPopupMenu
     */
    public function getPopupMenu() {
    }

    /**
     *
     * @param    IPopupMenu $Value
     */
    public function setPopupMenu($Value) {
    }

    /**
     *
     * @return    boolean
     */
    public function getParentShowHint() {
    }

    /**
     *
     * @param    boolean $Value
     */
    public function setParentShowHint($Value) {
    }

    /**
     *
     * @return    boolean
     */
    public function getShowHint() {
    }

    /**
     *
     * @param    boolean $Value
     */
    public function setShowHint($Value) {
    }

    /**
     *
     * @return    string
     */
    public function getHint() {
    }

    /**
     *
     * @param    string $Value
     */
    public function setHint($Value) {
    }

    /**
     *
     * @return    TDelegate
     */
    public function getOnClick() {
    }

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnClick($Value) {
    }

    /**
     *
     * @return    TDelegate
     */
    public function getOnContextPopup() {
    }

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnContextPopup($Value) {
    }

    /**
     *
     * @return    TDelegate
     */
    public function getOnDoubleClick() {
    }

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnDoubleClick($Value) {
    }

    /**
     *
     * @return    TDelegate
     */
    public function getOnDragDrop() {
    }

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnDragDrop($Value) {
    }

    /**
     *
     * @return    TDelegate
     */
    public function getOnDragOver() {
    }

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnDragOver($Value) {
    }

    /**
     *
     * @return    TDelegate
     */
    public function getOnStartDrag() {
    }

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnStartDrag($Value) {
    }

    /**
     *
     * @return    TDelegate
     */
    public function getOnEndDrag() {
    }

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnEndDrag($Value) {
    }

    /**
     *
     * @return    TDelegate
     */
    public function getOnMouseDown() {
    }

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnMouseDown($Value) {
    }

    /**
     *
     * @return    TDelegate
     */
    public function getOnMouseEnter() {
    }

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnMouseEnter($Value) {
    }

    /**
     *
     * @return    TDelegate
     */
    public function getOnMouseLeave() {
    }

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnMouseLeave($Value) {
    }

    /**
     *
     * @return    TDelegate
     */
    public function getOnMouseMove() {
    }

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnMouseMove($Value) {
    }

    /**
     *
     * @return    TDelegate
     */
    public function getOnMouseUp() {
    }

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnMouseUp($Value) {
    }

    /**
     *
     * @return    TDelegate
     */
    public function getOnMouseWheel() {
    }

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnMouseWheel($Value) {
    }

    /**
     *
     * @return    TDelegate
     */
    public function getOnMouseWheelDown() {
    }

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnMouseWheelDown($Value) {
    }

    /**
     *
     * @return    TDelegate
     */
    public function getOnMouseWheelUp() {
    }

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnMouseWheelUp($Value) {
    }

    /**
     *
     * @return    TDelegate
     */
    public function getOnResize() {
    }

    /**
     *
     * @param    TDelegate $Value
     */
    public function setOnResize($Value) {
    }
}

//TODO private FWindowProc: TWndMethod
//TODO private FControlStyle: TControlStyle
//TODO private FControlState: TControlState
//TODO what about touching ability?

/**
 * Interface IApplication
 * @package FrameworkDSW\CoreClasses
 */
interface IApplication extends IInterface {
    /**
     * @return \FrameworkDSW\Controller\IControllerManager
     */
    public function getControllerManager();

    /**
     * @return \FrameworkDSW\System\TExceptionHandler
     */
    public function getExceptionHandler();

    /**
     * Run
     */
    public function Run();

    /**
     * Quit
     */
    public function Quit();
}

/**
 * IPopupMenu
 * @author    许子健
 */
interface IPopupMenu extends IControl { //TODO: impl IPopupMenu.
}

/**
 * ILayoutManager
 * @author    许子健
 */
interface ILayoutManager extends IInterface {

    /**
     *
     * @param    IControl $Control
     */
    public function AddLayoutControl($Control);

    /**
     *
     * @param    IControl $Control
     */
    public function LayoutContainer($Control);

    /**
     *
     * @param    IControl $Control
     * @return    TSize
     */
    public function MinimumLayoutSize($Control);

    /**
     *
     * @param    IControl $Control
     * @return    TSize
     */
    public function PreferredLayoutSize($Control);

    /**
     *
     * @param    IControl $Control
     */
    public function RemoveLayoutControl($Control);
}