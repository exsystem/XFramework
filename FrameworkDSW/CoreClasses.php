<?php
/**
 * CoreClasses
 * @author	许子健
 * @version	$Id$
 * @since	separate file since reversion 1
 */
require_once 'FrameworkDSW/System.php';

class EComponentErr extends EException {}
class EInvalidComponentName extends EComponentErr {}
class ENoSuchComponent extends EComponentErr {}
class ENoOwnerComponent extends EComponentErr {}

/**
 * TOperation
 * @author	许子健
 */
final class TOperation extends TEnum {
    const eInsert = 0, eRemove = 1;
}

/**
 * TCursor
 * @author	许子健
 */
final class TCursor extends TEnum {
    const eDefault = 0;
    const eNone = -1;
    const eArrow = -2;
    const eCross = -3;
    const eIBeam = -4;
    const eSize = -22;
    const eSizeNESW = -6;
    const eSizeNS = -7;
    const eSizeNWSE = -8;
    const eSizeWE = -9;
    const eUpArrow = -10;
    const eHourGlass = -11;
    const eDrag = -12;
    const eNoDrag = -13;
    const eHSplit = -14;
    const eVSplit = -15;
    const eMultiDrag = -16;
    const eSQLWait = -17;
    const eNo = -18;
    const eAppStart = -19;
    const eHelp = -20;
    const eHandPoint = -21;
    const eSizeAll = -22;
}

/**
 * TDragState
 * @author	许子健
 */
final class TDragState extends TEnum {
    const eDragEnter = 0, eDragLeave = 1, eDragMove = 2;
}

/**
 * TDragDropObject
 * @author	许子健
 */
class TDragDropObject extends TObject {//TODO: impl TDragDropObject.
}

/**
 * TShiftState
 * @author	许子健
 */
final class TShiftState extends TSet {
    const eShift = 0, eAlt = 1, eCtrl = 2;
    const eLeft = 3, eRight = 4, eMiddle = 5;
    const eDouble = 6, eTouch = 7, ePen = 8;
}

/**
 * TMouseButton
 * @author	许子健
 */
final class TMouseButton extends TSet {
    const eLeft = 0, eRight = 1, eMiddle = 2;
}

/**
 *
 * @author	许子健
 */
final class TAlign extends TEnum {
    const eNone = 0;
    const eTopLeft = 1, eTop = 2, eTopRight = 3;
    const eLeft = 4, eClient = 5, eRight = 6;
    const eBottomLeft = 7, eBottom = 8, eBottomRight = 9;
}
//TODO: think about IAlign.


/**
 * TNotifyEvent
 * @author	许子健
 */
interface TNotifyEvent extends IDelegate {

    /**
     *
     * @param	TObject	$Sender
     */
    public function Invoke($Sender);
}

/**
 * TMouseEvent
 * @author	许子健
 */
interface TMouseEvent extends IDelegate {

    /**
     *
     * @param	TObject			$Sender
     * @param	TMouseButton	$Button
     * @param	TShiftState		$Shift
     * @param	integer			$X
     * @param	integer			$Y
     */
    public function Invoke($Sender, $Button, $Shift, $X, $Y);
}

/**
 * TContextPopupEvent
 * @author	许子健
 */
interface TContextPopupEvent extends IDelegate {

    /**
     *
     * @param	TObject	$Sender
     * @param	integer	$X
     * @param	integer	$Y
     * @param	boolean	$Handled
     */
    public function Invoke($Sender, $X, $Y, &$Handled);
}

/**
 * TDragOverEvent
 * @author 许子健
 */
interface TDragOverEvent extends IDelegate {

    /**
     *
     * @param	TObject		$Sender
     * @param	TObject		$Source
     * @param	integer		$X
     * @param	integer		$Y
     * @param	TDragState	$State
     * @param	boolean		$Accept
     */
    public function Invoke($Sender, $Source, $X, $Y, $State, &$Accept);
}

/**
 * TDragDropEvent
 * @author	许子健
 */
interface TDragDropEvent extends IDelegate {

    /**
     *
     * @param	TObject			$Sender
     * @param	TDragDropObject	$Source
     * @param	integer			$X
     * @param	integer			$Y
     */
    public function Invoke($Sender, $Source, $X, $Y);
}

/**
 *
 * @author	许子健
 */
interface TStartDragEvent extends IDelegate {

    /**
     *
     * @param	TObject		$Sender
     * @param	TDragObject	$DragObject
     */
    public function Invoke($Sender, &$DragObject);
}

/**
 * TEndDragEvent
 * @author	许子健
 */
interface TEndDragEvent extends IDelegate {

    /**
     *
     * @param	TObject	$Sender
     * @param	TObject	$Target
     * @param	integer	$X
     * @param	integer	$Y
     */
    public function Invoke($Sender, $Target, $X, $Y);
}

/**
 * TMouseWheelEvent
 * @author	许子健
 */
interface TMouseWheelEvent extends IDelegate {

    /**
     *
     * @param	TObject		$Sender
     * @param	TShiftState	$Shift
     * @param	integer		$WheelDelta
     * @param	integer		$X
     * @param	integer		$Y
     * @param	boolean		$Handled
     */
    public function Invoke($Sender, $Shift, $WheelDelta, $X, $Y, &$Handled);
}

/**
 * TMouseWheelUpDownEvent
 * @author	许子健
 */
interface TMouseWheelUpDownEvent extends IDelegate {

    /**
     *
     * @param	TObject		$Sender
     * @param	TShiftState	$Shift
     * @param	integer		$X
     * @param	integer		$Y
     * @param	boolean		$Handled
     */
    public function Invoke($Sender, $Shift, $X, $Y, &$Handled);
}

/**
 * IComponent
 * @author	许子健
 */
interface IComponent extends IInterface {

    /**
     *
     * @param	IComponent	$Owner
     */
    public function __construct($Owner);

    /**
     *
     * @param	IComponent	$Component
     */
    public function InsertComponent($Component);

    /**
     *
     * @param	IComponent	$Component
     */
    public function RemoveComponent($Component);

    /**
     *
     * @param	string		$Name
     * @return	IComponent
     */
    public function FindComponent($Name);

    /**
     *
     * @return	string
     */
    public function getName();

    /**
     *
     * @param	string	$Value
     */
    public function setName($Value);

    /**
     *
     * @return	IComponent
     */
    public function getOwner();

    /**
     *
     * @return	integer
     */
    public function getComponentCount();
}

/**
 * IView
 * @author	许子健
 */
interface IView extends IInterface {//extends IComponent {
    /**
     * descHere
     * @param	IMap	$ViewData <K: string, V: IInterface>
     */
    public function Update($ViewData);
}

/**
 * TComponent class
 * @author  许子健
 */
abstract class TComponent extends TObject implements IComponent {
    /**
     *
     * @var	string
     */
    const CValidIdentPattern = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/';

    /**
     *
     * @var	string
     */
    private $FName = '';
    /**
     * @var	mixed
     */
    private $FTag = null;
    /**
     *
     * @var	TComponent
     */
    private $FOwner = null;
    /**
     *
     * @var	TList
     */
    private $FComponents = null; //TODO: what about using a linked list?


    /**
     * @param  TComponent  $Owner
     * @see FrameworkDSW/TObject#Create()
     */
    public function __construct($Owner = null) {
        parent::__construct();
        TType::Object($Owner, 'TComponent');

        if (isset($Owner)) {
            $Owner->InsertComponent($this);
            $this->FOwner = $Owner;
        }
    }

    /**
     * (non-PHPdoc)
     * @see FrameworkDSW/TObject#Destroy()
     */
    public function Destroy() {
        if (isset($this->FOwner)) {
            $this->FOwner->RemoveComponent($this);
        }

        parent::Destroy();
    }

    /**
     *
     * @param	string	$Name
     */
    private static function ValidateName($Name) {
        if (preg_match(self::CValidIdentPattern, $Name) != 1) {
            throw new EInvalidComponentName();
        }
    }

    /**
     *
     * @param	TComponent	$Component
     */
    private function Insert($Component) {
        if (!isset($this->FComponents)) {
            $this->FComponents = new TList();
            $Component->FOwner = $this;
        }
    }

    /**
     *
     * @param	TComponent	$Component
     */
    private function Remove($Component) {
        unset($Component->FOwner);
        $this->FComponents->Remove($Component);
        if ($this->FComponents->Size() == 0) {
            Framework::Free($this->FComponents);
        }
    }

    /**
     *
     * @param	TComponent	$Component
     * @param	TOperation	$Operation
     */
    protected function Notify($Component, $Operation) {
        TType::Object($Component, 'TComponent');
        TType::Object($Operation, 'TOperation');

        if (isset($this->FComponents)) {
            foreach ($this->FComponents as $mComponent) {
                $mComponent->Notify($Component, $Operation);
            }
        }
    }

    /**
     *
     * @param	IComponent	$Component
     */
    public function InsertComponent($Component) {
        TType::Object($Component, 'TComponent');

        if (isset($Component->FOwner)) {
            $Component->FOwner->Remove($Component);
        }
        $this->Insert($Component);
        $this->Notify($Component, TOperation::eInsert());
    }

    /**
     *
     * @param	IComponent	$Component
     */
    public function RemoveComponent($Component) {
        TType::Object($Component, 'TComponent');

        $this->Notify($Component, TOperation::eRemove());
        $this->Remove($Component);
    }

    /**
     *
     * @param	string		$Name
     * @return	IComponent
     */
    public function FindComponent($Name) {
        TType::String($Name);

        self::ValidateName($Name);
        if (isset($this->FComponents)) {
            foreach ($this->FComponents as $mComponent) {
                if ($mComponent->getName() == $Name) {
                    return $mComponent;
                }
            }
        }

        throw new ENoSuchComponent();
    }

    /**
     *
     * @return	string
     */
    public function getName() {
        return $this->FName;
    }

    /**
     *
     * @param	string	$Value
     */
    public function setName($Value) {
        TType::String($Value);

        if ($Value != $this->FName) {
            if ($Value == '') {
                $this->FName = '';
                return;
            }

            if (isset($this->FOwner)) {
                try {
                    $this->FOwner->FindComponent($Value);
                    throw new EInvalidComponentName();
                }
                catch (ENoSuchComponent $e) {
                    $this->FName = $Value;
                }
            }
            else {
                self::ValidateName($Value);
                $this->FName = $Value;
            }
        }
    }

    /**
     *
     * @return	IComponent
     */
    public function getOwner() {
        return $this->FOwner;
    }

    /**
     *
     * @param	integer		$Index
     * @return	TComponent
     */
    public function getComponent($Index) {
        if (!isset($this->FComponents)) {
            throw new EIndexOutOfBounds();
        }

        return $this->FComponents[$Index];
    }

    /**
     *
     * @return	integer
     */
    public function getComponentCount() {
        if (!isset($this->FComponents)) {
            return 0;
        }
        return $this->FComponents->Size();
    }

    /**
     *
     * @return	integer
     */
    public function getComponentIndex() {
        if (!isset($this->FOwner)) {
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
     * @param  mixed   $Value
     */
    public function setTag($Value) {
        $this->FTag = $Value;
    }
}
//TODO: TComponentState


/**
 * IControl
 * @author	许子健
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
     * @return	string
     */
    public function getName();

    /**
     *
     * @param	strign	$Value
     */
    public function setName($Value);

    /**
     *
     * @return	integer
     */
    public function getLeft();

    /**
     *
     * @return	integer
     */
    public function getTop();

    /**
     *
     * @return	integer
     */
    public function getWidth();

    /**
     *
     * @return	integer
     */
    public function getHeight();

    /**
     *
     * @return	TPoint
     */
    public function getPosition();

    /**
     *
     * @param	TPoint	$Value
     */
    public function setPosition($Value);

    /**
     *
     * @return	TSize
     */
    public function getSize();

    /**
     *
     * @param	TSize	$Value
     */
    public function setSize($Value);

    /**
     *
     * @return	boolean
     */
    public function getVisible();

    /**
     *
     * @param	boolean	$Value
     */
    public function setVisible($Value);

    /**
     *
     * @return	boolean
     */
    public function getEnabled();

    /**
     *
     * @param	boolean	$Value
     */
    public function setEnabled($Value);

    /**
     *
     * @return	IControl
     */
    public function getParent();

    /**
     *
     * @param	IControl	$Value
     */
    public function setParent($Value);

    /**
     *
     * @return	boolean
     */
    public function getParentFont();

    /**
     *
     * @param	boolean	$Value
     */
    public function setParentFont($Value);

    /**
     *
     * @return	boolean
     */
    public function getParentColor();

    /**
     *
     * @param	boolean	$Value
     */
    public function setParentColor($Value);

    /**
     *
     * @return	TFont
     */
    public function getFont();

    /**
     *
     * @param	TFont	$Value
     */
    public function setFont($Value);

    /**
     *
     * @return	TColor
     */
    public function getColor();

    /**
     *
     * @param	TColor	$Value
     */
    public function setColor($Value);

    /**
     *
     * @return	string
     */
    public function getText();

    /**
     *
     * @param	string	$Value
     */
    public function setText($Value);

    /**
     *
     * @return	TCursor
     */
    public function getCursor();

    /**
     *
     * @param	TCursor	$Value
     */
    public function setCursor($Value);

    /**
     *
     * @return	TCursor
     */
    public function getDragCursor();

    /**
     *
     * @param	TCursor	$Value
     */
    public function setDragCursor($Value);

    /**
     *
     * @return	IPopupMenu
     */
    public function getPopupMenu();

    /**
     *
     * @param	IPopupMenu	$Value
     */
    public function setPopupMenu($Value);

    /**
     *
     * @return	boolean
     */
    public function getParentShowHint();

    /**
     *
     * @param	boolean	$Value
     */
    public function setParentShowHint($Value);

    /**
     *
     * @return	boolean
     */
    public function getShowHint();

    /**
     *
     * @param	boolean	$Value
     */
    public function setShowHint($Value);

    /**
     *
     * @return	string
     */
    public function getHint();

    /**
     *
     * @param	string	$Value
     */
    public function setHint($Value);

    /**
     *
     * @return	TDelegate
     */
    public function getOnClick();

    /**
     *
     * @param	TDelegate	$Value
     */
    public function setOnClick($Value);

    /**
     *
     * @return	TDelegate
     */
    public function getOnContextPopup();

    /**
     *
     * @param	TDelegate	$Value
     */
    public function setOnContextPopup($Value);

    /**
     *
     * @return	TDelegate
     */
    public function getOnDoubleClick();

    /**
     *
     * @param	TDelegate	$Value
     */
    public function setOnDoubleClick($Value);

    /**
     *
     * @return	TDelegate
     */
    public function getOnDragDrop();

    /**
     *
     * @param	TDelegate	$Value
     */
    public function setOnDragDrop($Value);

    /**
     *
     * @return	TDelegate
     */
    public function getOnDragOver();

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnDragOver($Value);

    /**
     *
     *	@return	TDelegate
     */
    public function getOnStartDrag();

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnStartDrag($Value);

    /**
     *
     *	@return	TDelegate
     */
    public function getOnEndDrag();

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnEndDrag($Value);

    /**
     *
     *	@return	TDelegate
     */
    public function getOnMouseDown();

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnMouseDown($Value);

    /**
     *
     *	@return	TDelegate
     */
    public function getOnMouseEnter();

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnMouseEnter($Value);

    /**
     *
     *	@return	TDelegate
     */
    public function getOnMouseLeave();

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnMouseLeave($Value);

    /**
     *
     *	@return	TDelegate
     */
    public function getOnMouseMove();

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnMouseMove($Value);

    /**
     *
     *	@return	TDelegate
     */
    public function getOnMouseUp();

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnMouseUp($Value);

    /**
     *
     *	@return	TDelegate
     */
    public function getOnMouseWheel();

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnMouseWheel($Value);

    /**
     *
     *	@return	TDelegate
     */
    public function getOnMouseWheelDown();

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnMouseWheelDown($Value);

    /**
     *
     *	@return	TDelegate
     */
    public function getOnMouseWheelUp();

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnMouseWheelUp($Value);

    /**
     *
     *	@return	TDelegate
     */
    public function getOnResize();

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnResize($Value);
}

/**
 *
 * @author	许子健
 */
abstract class TControl extends TComponent implements IControl {
    /**
     *
     * @var	TControl
     */
    private $FParent = null;
    /**
     *
     * @var	integer
     */
    private $FLeft = 0;
    /**
     *
     * @var	integer
     */
    private $FTop = 0;
    /**
     *
     * @var	integer
     */
    private $FWidth = 0;
    /**
     *
     * @var	integer
     */
    private $FHeight = 0;
    /**
     *
     * @var	boolean
     */
    private $FVisible = false;
    /**
     *
     * @var	boolean
     */
    private $FEnabled = false;
    /**
     *
     * @var	boolean
     */
    private $FParentFont = false;
    /**
     *
     * @var	boolean
     */
    private $FParentColor = false;
    /**
     *
     * @var	TAlign
     */
    private $FAlign = null;
    /**
     *
     * @var	boolean
     */
    private $FAutoSize = false;
    /**
     *
     * @var	TDragMode
     */
    private $FDragMode = null;
    /**
     *
     * @var	TFont
     */
    private $FFont = null;
    /**
     *
     * @var	TColor
     */
    private $FColor = null;
    /**
     *
     * @var	TCursor
     */
    private $FCursor = null;
    /**
     *
     * @var	TCursor
     */
    private $FDragCursor = null;
    /**
     *
     * @var	IPopMenu
     */
    private $FPopupMenu = null;
    /**
     *
     * @var	string
     */
    private $FHint = '';
    /**
     *
     * @var	boolean
     */
    private $FShowHint = false;
    /**
     *
     * @var	boolean
     */
    private $FParentHint = false;
    /**
     *
     * @var	TDragKind
     */
    private $FDragKind = null;
    /**
     *
     * @var	TDelegate
     */
    private $FOnMouseDown = null;
    /**
     *
     * @var	TDelegate
     */
    private $FOnMouseMove = null;
    /**
     *
     * @var	TDelegate
     */
    private $FOnMouseUp = null;
    /**
     *
     * @var	TDelegate
     */
    private $FOnDragDrop = null;
    /**
     *
     * @var	TDelegate
     */
    private $FOnDragOver = null;
    /**
     *
     * @var	TDelegate
     */
    private $FOnResize = null;
    /**
     *
     * @var	TDelegate
     */
    private $FOnStartDrag = null;
    /**
     *
     * @var	TDelegate
     */
    private $FOnEndDrag = null;
    /**
     *
     * @var	TDelegate
     */
    private $FOnClick = null;
    /**
     *
     * @var	TDelegate
     */
    private $FOnDoubleClick = null;
    /**
     *
     * @var	TDelegate
     */
    private $FOnContextPopup = null;
    /**
     *
     * @var	TDelegate
     */
    private $FOnMouseLeave = null;
    /**
     *
     * @var	TDelegate
     */
    private $FOnMouseEnter = null;
    /**
     *
     * @var	TDelegate
     */
    private $FOnMouseWheel = null;
    /**
     *
     * @var	TDelegate
     */
    private $FOnMouseWheelDown = null;
    /**
     *
     * @var	TDelegate
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
     * @param	TMouseButton	$Button
     * @param	TShiftState		$Shift
     * @param	integer			$X
     * @param	integer			$Y
     */
    protected function slotMouseDown($Button, $Shift, $X, $Y) {
        if (isset($this->FOnMouseDown)) {
            $this->FOnMouseDown($this, $Button, $Shift, $X, $Y);
        }
    }

    /**
     *
     * @param	TMouseButton	$Button
     * @param	TShiftState		$Shift
     * @param	integer			$X
     * @param	integer			$Y
     */
    protected function slotMouseUp($Button, $Shift, $X, $Y) {
        if (isset($this->FOnMouseUp)) {
            $this->FOnMouseUp($this, $Button, $Shift, $X, $Y);
        }
    }

    /**
     *
     * @param	TMouseButton	$Button
     * @param	TShiftState		$Shift
     * @param	integer			$X
     * @param	integer			$Y
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
     * @return	string
     */
    public function getName() {
    }

    /**
     *
     * @param	strign	$Value
     */
    public function setName($Value) {
    }

    /**
     *
     * @return	integer
     */
    public function getLeft() {
    }

    /**
     *
     * @return	integer
     */
    public function getTop() {
    }

    /**
     *
     * @return	integer
     */
    public function getWidth() {
    }

    /**
     *
     * @return	integer
     */
    public function getHeight() {
    }

    /**
     *
     * @return	TPoint
     */
    public function getPosition() {
    }

    /**
     *
     * @param	TPoint	$Value
     */
    public function setPosition($Value) {
    }

    /**
     *
     * @return	TSize
     */
    public function getSize() {
    }

    /**
     *
     * @param	TSize	$Value
     */
    public function setSize($Value) {
    }

    /**
     *
     * @return	boolean
     */
    public function getVisible() {
    }

    /**
     *
     * @param	boolean	$Value
     */
    public function setVisible($Value) {
    }

    /**
     *
     * @return	boolean
     */
    public function getEnabled() {
    }

    /**
     *
     * @param	boolean	$Value
     */
    public function setEnabled($Value) {
    }

    /**
     *
     * @return	IControl
     */
    public function getParent() {
    }

    /**
     *
     * @param	IControl	$Value
     */
    public function setParent($Value) {
    }

    /**
     *
     * @return	boolean
     */
    public function getParentFont() {
    }

    /**
     *
     * @param	boolean	$Value
     */
    public function setParentFont($Value) {
    }

    /**
     *
     * @return	boolean
     */
    public function getParentColor() {
    }

    /**
     *
     * @param	boolean	$Value
     */
    public function setParentColor($Value) {
    }

    /**
     *
     * @return	TFont
     */
    public function getFont() {
    }

    /**
     *
     * @param	TFont	$Value
     */
    public function setFont($Value) {
    }

    /**
     *
     * @return	TColor
     */
    public function getColor() {
    }

    /**
     *
     * @param	TColor	$Value
     */
    public function setColor($Value) {
    }

    /**
     *
     * @return	string
     */
    public function getText() {
    }

    /**
     *
     * @param	string	$Value
     */
    public function setText($Value) {
    }

    /**
     *
     * @return	TCursor
     */
    public function getCursor() {
    }

    /**
     *
     * @param	TCursor	$Value
     */
    public function setCursor($Value) {
    }

    /**
     *
     * @return	TCursor
     */
    public function getDragCursor() {
    }

    /**
     *
     * @param	TCursor	$Value
     */
    public function setDragCursor($Value) {
    }

    /**
     *
     * @return	IPopupMenu
     */
    public function getPopupMenu() {
    }

    /**
     *
     * @param	IPopupMenu	$Value
     */
    public function setPopupMenu($Value) {
    }

    /**
     *
     * @return	boolean
     */
    public function getParentShowHint() {
    }

    /**
     *
     * @param	boolean	$Value
     */
    public function setParentShowHint($Value) {
    }

    /**
     *
     * @return	boolean
     */
    public function getShowHint() {
    }

    /**
     *
     * @param	boolean	$Value
     */
    public function setShowHint($Value) {
    }

    /**
     *
     * @return	string
     */
    public function getHint() {
    }

    /**
     *
     * @param	string	$Value
     */
    public function setHint($Value) {
    }

    /**
     *
     * @return	TDelegate
     */
    public function getOnClick() {
    }

    /**
     *
     * @param	TDelegate	$Value
     */
    public function setOnClick($Value) {
    }

    /**
     *
     * @return	TDelegate
     */
    public function getOnContextPopup() {
    }

    /**
     *
     * @param	TDelegate	$Value
     */
    public function setOnContextPopup($Value) {
    }

    /**
     *
     * @return	TDelegate
     */
    public function getOnDoubleClick() {
    }

    /**
     *
     * @param	TDelegate	$Value
     */
    public function setOnDoubleClick($Value) {
    }

    /**
     *
     * @return	TDelegate
     */
    public function getOnDragDrop() {
    }

    /**
     *
     * @param	TDelegate	$Value
     */
    public function setOnDragDrop($Value) {
    }

    /**
     *
     * @return	TDelegate
     */
    public function getOnDragOver() {
    }

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnDragOver($Value) {
    }

    /**
     *
     *	@return	TDelegate
     */
    public function getOnStartDrag() {
    }

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnStartDrag($Value) {
    }

    /**
     *
     *	@return	TDelegate
     */
    public function getOnEndDrag() {
    }

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnEndDrag($Value) {
    }

    /**
     *
     *	@return	TDelegate
     */
    public function getOnMouseDown() {
    }

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnMouseDown($Value) {
    }

    /**
     *
     *	@return	TDelegate
     */
    public function getOnMouseEnter() {
    }

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnMouseEnter($Value) {
    }

    /**
     *
     *	@return	TDelegate
     */
    public function getOnMouseLeave() {
    }

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnMouseLeave($Value) {
    }

    /**
     *
     *	@return	TDelegate
     */
    public function getOnMouseMove() {
    }

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnMouseMove($Value) {
    }

    /**
     *
     *	@return	TDelegate
     */
    public function getOnMouseUp() {
    }

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnMouseUp($Value) {
    }

    /**
     *
     *	@return	TDelegate
     */
    public function getOnMouseWheel() {
    }

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnMouseWheel($Value) {
    }

    /**
     *
     *	@return	TDelegate
     */
    public function getOnMouseWheelDown() {
    }

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnMouseWheelDown($Value) {
    }

    /**
     *
     *	@return	TDelegate
     */
    public function getOnMouseWheelUp() {
    }

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnMouseWheelUp($Value) {
    }

    /**
     *
     *	@return	TDelegate
     */
    public function getOnResize() {
    }

    /**
     *
     *	@param	TDelegate	$Value
     */
    public function setOnResize($Value) {
    }
}
//TODO private FWindowProc: TWndMethod
//TODO private FControlStyle: TControlStyle
//TODO private FControlState: TControlState
//TODO what about touching ablity?


/**
 * IPopupMenu
 * @author	许子健
 */
interface IPopupMenu extends IControl {//TODO: impl IPopupMenu.
}

/**
 * ILayoutManager
 * @author	许子健
 */
interface ILayoutManager extends IInterface {

    /**
     *
     * @param	IControl	$Control
     */
    public function AddLayoutControl($Control);

    /**
     *
     * @param	IControl	$Control
     */
    public function LayoutContainer($Control);

    /**
     *
     * @param	IControl	$Control
     * @return	TSize
     */
    public function MininumLayoutSize($Control);

    /**
     *
     * @param	IControl	$Control
     * @return	TSize
     */
    public function PreferredLayoutSize($Control);

    /**
     *
     * @param	IControl	$Control
     */
    public function RemoveLayoutControl($Control);
}