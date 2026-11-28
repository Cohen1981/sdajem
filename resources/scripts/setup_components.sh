config_read_file() {
    (grep -E "^${2}=" -m 1 "${1}" 2>/dev/null || echo "VAR=__UNDEFINED__") | head -n 1 | cut -d '=' -f 2-;
}

config_get() {
  # .env when started in make context. Else change to ../../.env
    val="$(config_read_file "${PWD}"/.env "${1}")";
    if [ "${val}" = "__UNDEFINED__" ]; then
        val="$(config_read_file config.cfg.defaults "${1}")";
    fi
    printf -- "%s" "${val%%[[:cntrl:]]}";
}

upper_first ()
{
    printf "$1" | cut -c1 -z | tr -d '\0' | tr [:lower:] [:upper:]
    printf "$1" | cut -c2-
}

JOOMLA_FOLDER="$(config_get JOOMLA_FOLDER)";
target="${PWD}"/"${JOOMLA_FOLDER}";

while [ ! -f "${target}"/web.config.txt ] ;
do
    echo "Waiting for file synchronization !";
    sleep 2;
done

# Waiting for auto install of joomla
while [ -d "${target}"/installation ]
do
  echo "Waiting for joomla installation to complete";
  sleep 2;
done

namespaceRoot="$(config_get NAMESPACE_ROOT)";
components="$(config_get COMPONENTS)";
source="${PWD}"/code;

if [ "${components}" != "__UNDEFINED__" ]; then
  for component in $components
  do
    echo "setting up ${component}";
    echo "${source}";
    echo "${target}";
    mkdir -v -p "${source}"/"${namespaceRoot}"/Component/"$(upper_first "$component")"/Administrator;
    mkdir -v -p "${source}"/"${namespaceRoot}"/Component/"$(upper_first "$component")"/Site;
    mkdir -v -p "${source}"/media/com_"${component}"

    linkTest="${target}"/administrator/components/com_"${component}"
    echo "${linkTest}";
    echo "${source}/${namespaceRoot}/Component/$(upper_first $component)/Administrator";

    if [ ! -L "${linkTest}" ] && [ ! -e "${linkTest}" ] ;
      then
        ln -sr "${source}"/"${namespaceRoot}"/Component/"$(upper_first "$component")"/Administrator "${target}"/administrator/components/com_"${component}";
        ln -sr "${source}"/"${namespaceRoot}"/Component/"$(upper_first "$component")"/Site "${target}"/components/com_"${component}";
        ln -sr "${source}"/media/com_"${component}" "${target}"/media/com_"${component}";
      else
        echo "all linked";
    fi
  done
fi

modules="$(config_get MODULES)";
if [ "${modules}" != "__UNDEFINED__" ]; then
  for module in $modules
  do
    mkdir -v -p "${source}"/"${namespaceRoot}"/Module/"$(upper_first "$module")";

    linkTest="${target}"/modules/mod_"${module}"

    if [ ! -L "${linkTest}" ] && [ ! -e "${linkTest}" ] ; then
        ln -sr "${source}"/"${namespaceRoot}"/Module/"$(upper_first "$module")" "${target}"/modules/mod_"${module}";
    fi

  done
fi

#SITE_TEMPLATES
templates="$(config_get SITE_TEMPLATES)";
if [ "${templates}" != "__UNDEFINED__" ]; then
  for template in $templates
  do
    mkdir -v -p "${source}"/templates/"${template}";
    mkdir -v -p "${source}"/media/templates/site/"${template}";

     if ! [ -L "${target}"/templates/"${template}" ] ;
          then
            ln -sr "${source}"/templates/"${template}" "${target}"/templates/"${template}";
            ln -sr "${source}"/media/templates/site/"${template}" "${target}"/media/templates/site/"${template}";
      fi

  done
fi

#ADMIN_TEMPLATES
templates="$(config_get ADMIN_TEMPLATES)";
if [ "${templates}" != "__UNDEFINED__" ]; then
  for template in $templates
  do
    mkdir -v -p "${source}"/templates/"${template}";
    mkdir -v -p "${source}"/media/templates/administrator/"${template}";

     if ! [ -L "${target}"/templates/"${template}" ] ;
          then
            ln -sr "${source}"/templates/"${template}" "${target}"/templates/"${template}";
            ln -sr "${source}"/media/templates/administrator/"${template}" "${target}"/media/templates/administrator/"${template}";
      fi

  done
fi

#PLUGINS
