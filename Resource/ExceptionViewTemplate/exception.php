<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
    <title>XFramework</title>
</head>
<body>
<h1 id="exception-header">Uncaught Exception: <?= $this->Data('ClassName') ?></h1>

<h2 id="exception-info-header">Exception Info</h2>

<p id="exception-info">
    <span>Class Name: </span><?= $this->Data('ClassName') ?><br/>
    <span>Namespace: </span><?= $this->Data('Namespace') ?><br/>
    <span>File: </span><?= $this->Data('File') ?><br/>
    <span>Line Number: </span><?= $this->Data('Line') ?><br/>
    <span>Message: </span><?= $this->Data('Message') ?>
</p>

<h2>Call Stack</h2>
<ol id="call-stack">
    <?php foreach ($this->Data('CallStack') as $i): ?>
        <li class="call-stack-item">
            <?= $i->Method ?>() @ <?= $i->File ?> : <?= $i->Line ?>
            <ol class="parameters">
                Parameters
                <?php foreach ($i->Parameters as $j): ?>
                    <li class="parameter">
                        <span><?= $j->Key ?></span>
                    <pre>
                        <?php var_dump($j->Value); ?>
                    </pre>
                    </li>
                <?php endforeach; ?>
            </ol>
        </li>
    <?php endforeach; ?>
</ol>

<h2>Exception Stack</h2>
<ol id="exception-stack">
    <?php foreach ($this->Data('ExceptionStack') as $i): ?>
        <li class="exception-stack-item">
            <p class="exception-info-header">Exception Info</p>

            <p class="exception-info">
                <span>Class Name: </span><?= $i->ClassName ?><br/>
                <span>Namespace: </span><?= $i->Namespace ?><br/>
                <span>File: </span><?= $i->File ?><br/>
                <span>Line Number: </span><?= $i->Line ?><br/>
                <span>Message: </span><?= $i->Message ?>
            </p>

            <p class="call-stack-header">Call Stack</p>
            <ol class="call-stack">
                <?php foreach ($i->CallStack as $j): ?>
                    <li class="call-stack-item">
                        <?= $j->Method ?>() @ <?= $j->File ?> : <?= $j->Line ?>
                        <ol class="parameters">
                            Parameters
                            <?php foreach ($j->Parameters as $k): ?>
                                <li class="parameter">
                                    <span><?= $k->Key ?></span>
                                    <pre>
                                        <?php var_dump($k->Value); ?>
                                    </pre>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </li>
                <?php endforeach; ?>
            </ol>
        </li>
    <?php endforeach; ?>
</ol>
</body>
</html>