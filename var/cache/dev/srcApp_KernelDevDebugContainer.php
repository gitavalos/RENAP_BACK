<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerH926Vbf\srcApp_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerH926Vbf/srcApp_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerH926Vbf.legacy');

    return;
}

if (!\class_exists(srcApp_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerH926Vbf\srcApp_KernelDevDebugContainer::class, srcApp_KernelDevDebugContainer::class, false);
}

return new \ContainerH926Vbf\srcApp_KernelDevDebugContainer(array(
    'container.build_hash' => 'H926Vbf',
    'container.build_id' => '901fe092',
    'container.build_time' => 1545018546,
), __DIR__.\DIRECTORY_SEPARATOR.'ContainerH926Vbf');
